<?php

namespace Alex19pov31\Tests\BitrixFacetHelper\Stubs\Bitrix;

class Facet
{
    public $sectionID;
    public $numericFilter;
    public $dateTimeFilter;
    public $dictonaryFilter;
    public $priceFilter;
    private $isFiltered = false;

    public function getDictionary(): Dictionary
    {
        return new Dictionary();
    }

    public function addNumericPropertyFilter($propertyId, $operator, $value)
    {
        $this->numericFilter[$operator . $propertyId] = [
            'property_id' => $propertyId,
            'operator' => $operator,
            'value' => $value,
        ];
    }

    public function addDatetimePropertyFilter($propertyId, $operator, $value)
    {
        $this->dateTimeFilter[$operator . $propertyId] = [
            'property_id' => $propertyId,
            'operator' => $operator,
            'value' => $value,
        ];
    }

    public function addPriceFilter($propertyId, $operator, $value)
    {
        $this->priceFilter[$operator . $propertyId] = [
            'property_id' => $propertyId,
            'operator' => $operator,
            'value' => $value,
        ];
    }

    public function addDictionaryPropertyFilter($propertyId, $operator, $value)
    {
        $this->dictonaryFilter[$operator . $propertyId] = [
            'property_id' => $propertyId,
            'operator' => $operator,
            'value' => $value,
        ];
    }

    public function setSectionId(int $sectionID)
    {
        $this->sectionID = $sectionID;
        return $this;
    }

    public function query(array $filter = [])
    {
        if (!empty($filter) || $this->isFiltered) {
            return new DB\Result('filtered_facet');
        }

        return new DB\Result('unfiltered_facet');
    }
}
