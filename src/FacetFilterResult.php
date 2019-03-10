<?php

namespace Alex19pov31\BitrixFacetHelper;

use Illuminate\Support\Collection;

class FacetFilterResult extends Collection
{
    private $filter;

    use FacetTrait;

    public function __construct(array $items = [], array $filter = [])
    {
        $this->filter = $filter;
        parent::__construct($items);
        $this->setFilter();
    }

    /**
     * Свойство
     *
     * @param string $code
     * @return FacetProperty
     */
    public function getProperty(string $code): FacetProperty
    {
        if (empty($this->items[$code])) {
            return static::getFacetPropertyObject();
        }

        return $this->items[$code];
    }

    /**
     * Список значений без группировки по свойствам
     *
     * @return array
     */
    public function getValues(): Collection
    {
        $valueList = [];
        foreach ($this->items as $prop) {
            foreach ($prop->getValues() as $key => $item) {
                $valueList[$key] = $item;
            }
        }

        return new Collection($valueList);
    }

    private function setFilter()
    {
        $newFilter = [];
        foreach ($this->filter as $code => $values) {
            $code = str_replace('PROPERTY_', '', $code);
            if (strpos($code, '>') !== false) {
                $code = str_replace(['>', '='], '', $code);
                $newFilter[$code]['min'] = $values;
                continue;
            }
            if (strpos($code, '<') !== false) {
                $code = str_replace(['<', '='], '', $code);
                $newFilter[$code]['max'] = $values;
                continue;
            }

            $newFilter[$code] = $values;
        }

        foreach ($newFilter as $code => $values) {
            if (empty($this->items[$code])) {
                continue;
            }

            $this->items[$code]->setFilter((array) $values);
        }
    }

    /**
     * Возвращает выбранные значения
     *
     * @return array
     */
    public function getSelectedValues(): array
    {
        $result = [];
        foreach ($this->filter as $code => $values) {
            $code = str_replace('PROPERTY_', '', $code);
            if (empty($this->items[$code])) {
                continue;
            }

            $prop = $this->items[$code];
            if (strpos($code, '>') !== false) {
                continue;
            }
            if (strpos($code, '<') !== false) {
                continue;
            }

            $selectedValues = $prop->getSelectedValue();
            if (empty($selectedValues)) {
                continue;
            }

            $result = array_merge($result, $selectedValues);
        }

        return $result;
    }

    /**
     * Количество элементов при фильтрации
     *
     * @return integer
     */
    public function getElementCount(): int
    {
        $count = 0;
        $selectedProps = [];
        foreach ($this->items as $id => $prop) {
            foreach ($prop->getValues() as $key => $item) {
                if ($item->isSelected()) {
                    $selectedProps[$id] = $id;
                    $count += $item->getCountElement();
                }
            }
        }

        return (int) ($count / count($selectedProps));
    }
}
