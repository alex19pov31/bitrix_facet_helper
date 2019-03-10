<?php

namespace Alex19pov31\BitrixFacetHelper;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Iblock\SectionPropertyTable;
use Illuminate\Support\Collection;

class FacetProperty extends BaseModel
{
    use FacetTrait;

    private $values;
    private $diffValue = 0;

    public function __construct(array $prop)
    {
        static::includeModule('iblock');
        if (empty($prop)) {
            return;
        }

        $this->fields = $prop;
        $this->id = (int) $prop['ID'];
    }

    protected static function query(int $iblockId, array $filter = [])
    {
        $filter['IBLOCK_ID'] = $iblockId;
        return SectionPropertyTable::getList([
            'filter' => $filter,
            'select' => [
                'ID' => 'PROPERTY.ID',
                'IBLOCK_ID',
                'SECTION_ID',
                'SMART_FILTER',
                'CODE' => 'PROPERTY.CODE',
                'NAME' => 'PROPERTY.NAME',
                'PROPERTY_TYPE' => 'PROPERTY.PROPERTY_TYPE',
                'DEFAULT_VALUE' => 'PROPERTY.DEFAULT_VALUE',
                'ACTIVE' => 'PROPERTY.ACTIVE',
                'MULTIPLE' => 'PROPERTY.MULTIPLE',
                'IS_REQUIRED' => 'PROPERTY.IS_REQUIRED',
                'LIST_TYPE' => 'PROPERTY.LIST_TYPE',
                'USER_TYPE' => 'PROPERTY.USER_TYPE',
                'USER_TYPE_SETTINGS' => 'PROPERTY.USER_TYPE_SETTINGS',
                'HINT' => 'PROPERTY.HINT',
            ],
        ]);
    }

    /**
     * Список свойств для фасетного индекса
     *
     * @param integer $iblockId
     * @param array $filter
     * @return Collection
     */
    public static function getList(int $iblockId, array $filter = []): Collection
    {
        $propertyList = [];
        $res = static::query($iblockId, $filter);
        if (!$res) {
            return new Collection([]);
        }

        $storage = static::getStorageObject();
        while ($prop = $res->fetch()) {
            $prop['FACET_ID'] = $storage::propertyIdToFacetId($prop['ID']);
            $prop['USER_TYPE_SETTINGS'] = unserialize($prop['USER_TYPE_SETTINGS']);
            $propertyList[] = new static($prop);
        }

        $priceList = static::getPrices();
        $propertyList = array_merge($propertyList, $priceList);

        return new Collection($propertyList);
    }

    /**
     * Идентификатор свойства
     *
     * @return integer
     */
    public function getID(): int
    {
        return (int) $this->id;
    }

    public function getFacetID(): int
    {
        return (int) $this['FACET_ID'];
    }

    /**
     * Название свойства
     *
     * @return string
     */
    public function getName(): string
    {
        return (string) $this['NAME'];
    }

    /**
     * Код свойства
     *
     * @return string
     */
    public function getCode(): string
    {
        return (string) $this['CODE'];
    }

    /**
     * Базовый тип свойства
     *
     * @return string
     */
    public function getType(): string
    {
        return (string) $this['PROPERTY_TYPE'];
    }

    /**
     * Пользовательский тип свойства
     *
     * @return string
     */
    public function getUserType(): string
    {
        return (string) $this['USER_TYPE'];
    }

    /**
     * Подсказка
     *
     * @return string
     */
    public function getHint(): string
    {
        return (string) $this['HINT'];
    }

    /**
     * Значение по-умолчанию
     *
     * @return string
     */
    public function getDefaultValue(): string
    {
        return (string) $this['DEFAULT_VALUE'];
    }

    /**
     * Активность
     *
     * @return boolean
     */
    public function isActive(): bool
    {
        return $this['ACTIVE'] == 'Y';
    }

    /**
     * Множественное значение
     *
     * @return boolean
     */
    public function isMultiple(): bool
    {
        return $this['MULTIPLE'] == 'Y';
    }

    /**
     * Обязательное свойство
     *
     * @return boolean
     */
    public function isRequired(): bool
    {
        return $this['IS_REQUIRED'] == 'Y';
    }

    /**
     * Пустой пользовательский тип
     *
     * @return boolean
     */
    public function isEmptyUserType(): bool
    {
        return trim($this->getUserType()) == "";
    }

    /**
     * Числовое значение индекса
     *
     * @return boolean
     */
    public function isNumericProperty(): bool
    {
        return PropertyTable::TYPE_NUMBER == $this->getType()
        && $this->isEmptyUserType();
    }

    /**
     * Значение индекса с датой и временем
     *
     * @return boolean
     */
    public function isDatetimeProperty(): bool
    {
        return PropertyTable::TYPE_STRING == $this->getType()
        && $this->getUserType() == 'DateTime';
    }

    /**
     * Значение индекса с ценой
     *
     * @return boolean
     */
    public function isPriceProperty(): bool
    {
        return $this->getType() == 'PRICE';
    }

    /**
     * Значение индекса в виде списка
     *
     * @return boolean
     */
    public function isDictionaryProperty(): bool
    {
        return !$this->isNumericProperty() && !$this->isDatetimeProperty() && !$this->isPriceProperty();
    }

    /**
     * Является ценой
     *
     * @return boolean
     */
    public function isPrice(): bool
    {
        return (bool) $this['IS_PRICE'];
    }

    /**
     * Установить доступные значения
     *
     * @param array $values
     * @return void
     */
    public function setValues(array $values)
    {
        $this->values = [];
        foreach ($values as $key => $value) {
            $this->values[$key] = new FacetValue($value);
        }
    }

    /**
     * Список значений
     *
     * @return array
     */
    public function getValues(): array
    {
        return (array) $this->values;
    }

    /**
     * Список значений из словаря индекса
     *
     * @return array
     */
    public function getDictValues(): array
    {
        $valueList = [];
        foreach ($this->values as $key => $item) {
            $valueList[] = $item->getDictValue();
        }

        return $valueList;
    }

    public function setDiffValue(float $diff)
    {
        $this->diffValue = $diff;
    }

    /**
     * Минимальное значение (только для числового типа)
     *
     * @return float
     */
    public function getMinValue(): float
    {
        if (!$this->isNumericProperty() && !$this->isPrice()) {
            return 0.0;
        }

        $firstValue = $this->getFirstValue();
        if ($firstValue->getMinValue() == $firstValue->getOriginMinValue()) {
            return $firstValue->getMinValue() - $this->diffValue;
        }

        return $firstValue->getMinValue();
    }

    /**
     * Проверка значений числового типа и типа цены
     *
     * @return boolean
     */
    public function isValidValues(): bool
    {
        if (!$this->isNumericProperty() && !$this->isPrice()) {
            return true;
        }

        return $this->getOriginMinValue() < $this->getOriginMaxValue();
    }

    /**
     * Максимальное значение (только для числового типа)
     *
     * @return float
     */
    public function getMaxValue(): float
    {
        if (!$this->isNumericProperty() && !$this->isPrice()) {
            return 0.0;
        }

        $firstValue = $this->getFirstValue();
        if ($firstValue->getMaxValue() == $firstValue->getOriginMaxValue()) {
            return $firstValue->getMaxValue() + $this->diffValue;
        }

        return $firstValue->getMaxValue();
    }

    /**
     * Начальное минимальное значение
     *
     * @return float
     */
    public function getOriginMinValue(): float
    {
        if (!$this->isNumericProperty() && !$this->isPrice()) {
            return 0.0;
        }

        return $this->getFirstValue()->getOriginMinValue() - $this->diffValue;
    }

    /**
     * Начальное максимальное значение
     *
     * @return float
     */
    public function getOriginMaxValue(): float
    {
        if (!$this->isNumericProperty() && !$this->isPrice()) {
            return 0.0;
        }

        return $this->getFirstValue()->getOriginMaxValue() + $this->diffValue;
    }

    /**
     * Первое значение из списка
     *
     * @return FacetValue
     */
    public function getFirstValue(): FacetValue
    {
        foreach ($this->getValues() as $value) {
            return $value;
        }

        return new FacetValue([]);
    }

    /**
     * Установить значения для отображения
     *
     * @param array $displayValues
     * @return void
     */
    public function setDisplayValue(array $displayValues)
    {
        foreach ($this->values as $key => &$item) {
            $dictValue = $item->getDictValue();
            $item['DISPLAY_VALUE'] = !empty($displayValues[$dictValue]) ? (string) $displayValues[$dictValue] : '';
        }
    }

    public function setFilter(array $values)
    {
        if ($this->isNumericProperty()) {
            foreach ($this->getValues() as &$value) {
                if (!empty($values['min'])) {
                    $value['MIN_VALUE_NUM'] = $values['min'];
                }

                if (!empty($values['max'])) {
                    $value['MAX_VALUE_NUM'] = $values['max'];
                }
            }
            return;
        }

        foreach ($this->getValues() as &$value) {
            if (in_array($value->getDictValue(), $values)) {
                $value['SELECTED'] = true;
            }
        }
    }

    /**
     * Возвращает код свойства
     *
     * @param string $prop
     * @return string
     */
    public static function getPropertyCode(string $prop): string
    {
        $prop = strtoupper($prop);
        return str_replace(['PROPERTY_', '>', '<', '=', "%", '!'], '', $prop);
    }

    /**
     * Возвращает оператор
     *
     * @param string $prop
     * @return string
     */
    public static function getPropertyOperation(string $prop): string
    {
        if (!preg_match('/([\>\<\=\%\!]{1,2})/', $prop, $match)) {
            return "=";
        }

        $operator = $match[1];
        if ($operator == '<') {
            $operator = "<=";
        }
        if ($operator == '>') {
            $operator = ">=";
        }

        return $operator;
    }

    /**
     * Сортирует значения по указанному полю
     *
     * @param string $byField
     * @return FacetProperty
     */
    public function sortValues(string $byField = 'DICTIONARY_VALUE'): FacetProperty
    {
        $newValues = [];
        foreach ($this->getValues() as $value) {
            $key = $value[$byField];
            $newValues[$key] = $value;
        }

        $this->values = [];
        ksort($newValues);
        foreach ($newValues as $value) {
            $key = $value['FACET_ID'] . "_" . $value['VALUE'];
            $this->values[$key] = $value;
        }

        return $this;
    }

    /**
     * Сортирует значения в соотвествии с указанными правилами сортировки
     *
     * @param array $data
     * @param string $byField
     * @return FacetProperty
     */
    public function sortValuesByData(array $data, string $byField = 'DICTIONARY_VALUE'): FacetProperty
    {
        $dataValues = [];
        foreach ($this->getValues() as $value) {
            $key = $value[$byField];
            $value['SORT'] = (int) $data[$key];
            $dataValues[] = $value->getData();
        }

        $values = (new Collection($dataValues))->sortBy('SORT')->keyBy(function ($value) {
            return $value['FACET_ID'] . "_" . $value['VALUE'];
        })->toArray();
        $this->setValues($values);

        return $this;
    }

    /**
     * Выбранные значения
     *
     * @return array
     */
    public function getSelectedValue(): array
    {
        $result = [];
        foreach ($this->getValues() as $value) {
            if ($value->isSelected()) {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * Список цен
     *
     * @return array
     */
    protected static function getPrices(): array
    {
        static::includeModule('catalog');
        $groupTable = static::getGroupTableObject();
        $resPrice = $groupTable::getList();
        $dataPrice = [];

        $storage = static::getStorageObject();
        while ($price = $resPrice->fetch()) {
            $price['FACET_ID'] = $storage::priceIdToFacetId($price['ID']);
            $price['CODE'] = 'PRICE_' . $price['NAME'];
            $price['PROPERTY_TYPE'] = 'PRICE';
            $dataPrice[] = new static($price);
        }

        return $dataPrice;
    }
}
