<?php

namespace Alex19pov31\BitrixFacetHelper;

use Bitrix\Iblock\PropertyIndex\Facet;
use Illuminate\Support\Collection;

class FacetFilter
{
    use FacetTrait;

    private $iblockId;
    private $originalFacet;
    private $facet;
    private $propertyList;
    private $sectionId = 0;
    private $selectedProps;

    const TTL_DAY = 86400;

    public function __construct(string $iblockCode, int $sectionId = 0)
    {
        static::includeModule('iblock');

        $this->iblockId = $this->getIblockIdByCode($iblockCode);
        $this->sectionId = $sectionId;
    }

    /**
     * Идентификатор инфоблока
     *
     * @return integer
     */
    public function getIblockID(): int
    {
        return (int) $this->iblockId;
    }

    /**
     * Фасетный индекс
     *
     * @return Facet
     */
    public function getFacet()
    {
        if (!is_null($this->facet)) {
            return $this->facet;
        }

        $this->originalFacet = new Facet($this->iblockId);
        $this->facet = $this->originalFacet;
        return $this->facet;
    }

    /**
     * Список параметров
     *
     * @param integer $ttl
     * @return Collection
     */
    public function getPoropertyList(int $ttl = 0): Collection
    {
        if (!is_null($this->propertyList)) {
            return $this->propertyList;
        }

        $filter = [
            'SMART_FILTER' => 'Y',
            'PROPERTY.ACTIVE' => 'Y',
        ];
        if ($this->sectionId > 0) {
            $filter['SECTION_ID'] = $this->sectionId;
        }

        $facetProperty = static::getFacetPropertyObject();
        $this->propertyList = $facetProperty::getList(
            $this->getIblockID(),
            $filter,
            $ttl
        );

        return $this->propertyList;
    }

    /**
     * Сброс фильтров фасетного индекса
     *
     * @return FacetFilter
     */
    public function resetFlter(): FacetFilter
    {
        $this->facet = $this->originalFacet;
        return $this;
    }

    /**
     * Новый фильтр
     *
     * @param string $propertyCode
     * @param string $operator
     * @param [type] $value
     * @return FacetFilter
     */
    public function setFilter(string $propertyCode, string $operator, $value): FacetFilter
    {
        $this->resetFlter();
        return $this->addFilter($propertyCode, $operator, $value);
    }

    /**
     * Дополнение фильтра
     *
     * @param string $propertyCode
     * @param string $operator
     * @param [type] $value
     * @return FacetFilter
     */
    public function addFilter(string $propertyCode, string $operator, $value): FacetFilter
    {
        $listProps = $this->getPoropertyList()->keyBy('CODE');
        $prop = $listProps[$propertyCode];
        if (!$prop) {
            return $this;
        }

        if ($prop->isNumericProperty()) {
            $this->getFacet()->addNumericPropertyFilter($prop->getID(), $operator, $value);
            return $this;
        }

        if ($prop->isDatetimeProperty()) {
            $this->getFacet()->addDatetimePropertyFilter($prop->getID(), $operator, $value);
            return $this;
        }

        if ($prop->isPriceProperty()) {
            $this->getFacet()->addPriceFilter($prop->getID(), $operator, $value);
            return $this;
        }

        $valueID = $this->getFacet()->getDictionary()->getStringId($value);
        $this->getFacet()->addDictionaryPropertyFilter($prop->getID(), $operator, $valueID);
        return $this;
    }

    private function getListValues($filter = [], bool $isOrigin = false): array
    {
        $filter = $this->normalizeFilter($filter);
        $values = [];
        $res = $this->getFacet()->setSectionId($this->sectionId)->query($filter);
        $props = $this->getPoropertyList()->keyBy('FACET_ID');
        while ($item = $res->fetch()) {
            $item["DICTIONARY_VALUE"] = $this->getFacet()->getDictionary()->getStringById((int) $item['VALUE']);
            if ($isOrigin) {
                $item["ORIGIN_ELEMENT_COUNT"] = $item["ELEMENT_COUNT"];
                $item["ORIGIN_MIN_VALUE_NUM"] = $item["MIN_VALUE_NUM"];
                $item["ORIGIN_MAX_VALUE_NUM"] = $item["MAX_VALUE_NUM"];
                $prop = $props[$item['FACET_ID']];
                if ($prop && $prop->isDictionaryProperty()) {
                    $item["ELEMENT_COUNT"] = "0";
                    $item["MIN_VALUE_NUM"] = "0";
                    $item["MAX_VALUE_NUM"] = "0";
                }
            }
            $key = $item['FACET_ID'] . "_" . $item['VALUE'];
            $values[$key] = $item;
        }
        return $values;
    }

    private function mergeValues(array $originValues, array $curValues): array
    {
        $props = $this->getPoropertyList()->keyBy('FACET_ID');
        foreach ($originValues as $key => &$val) {
            $prop = $props[$val['FACET_ID']];
            if (!$prop || !$prop->isDictionaryProperty() && !$this->selectedProps[$prop['CODE']]) {
                continue;
            }

            $val["ELEMENT_COUNT"] = !empty($curValues[$key]) ? (string) $curValues[$key]["ELEMENT_COUNT"] : '';
            $val["MIN_VALUE_NUM"] = !empty($curValues[$key]) ? (string) $curValues[$key]["MIN_VALUE_NUM"] : '';
            $val["MAX_VALUE_NUM"] = !empty($curValues[$key]) ? (string) $curValues[$key]["MAX_VALUE_NUM"] : '';
        }

        return $originValues;
    }

    /**
     * Список свойств с доступными значениями
     *
     * @param array $filter
     * @return FacetFilterResult
     */
    public function getList($filter = []): FacetFilterResult
    {
        $curValues = $this->getListValues($filter);
        $originValues = $this->resetFlter()->getListValues([], true);
        $values = (new Collection($this->mergeValues($originValues, $curValues)))->groupBy('FACET_ID');
        $props = $this->getPoropertyList()->keyBy('FACET_ID');
        foreach ($values as $facetID => $group) {
            $prop = $props[$facetID];
            if (!$prop) {
                continue;
            }

            $group = $group->keyBy(function ($item) {
                return $item['FACET_ID'] . "_" . $item['VALUE'];
            })->toArray();

            $prop->setValues($group);
            $dataList[$prop->getCode()] = $prop;
        }

        return new FacetFilterResult($dataList, $filter);
    }

    public function normalizeFilter($filter): array
    {
        $props = $this->getPoropertyList()->keyBy('CODE');
        $simpleFilter = [];
        foreach ($filter as $prop => $value) {
            $facetProperty = static::getFacetPropertyObject();
            $code = $facetProperty::getPropertyCode($prop);
            if (empty($props[$code]) || $props[$code]->isNumericProperty()) {
                $simpleFilter[$prop] = $value;
                continue;
            }

            $operation = $facetProperty::getPropertyOperation($prop);
            $this->selectedProps[$code] = [
                'operation' => $operation,
                'value' => $value,
            ];

            if (!is_array($value)) {
                $this->addFilter($code, $operation, $value);
                continue;
            }

            foreach ($value as $val) {
                $this->addFilter($code, $operation, $val);
            }
        }

        return $simpleFilter;
    }
}
