<?php

namespace Alex19pov31\Tests\BitrixFacetHelper;

use Alex19pov31\BitrixFacetHelper\FacetFilterResult;
use Alex19pov31\Tests\BitrixFacetHelper\Stubs\FacetFilter;
use PHPUnit\Framework\TestCase;

class FacetResultTest extends TestCase
{
    private $facetResult;

    public function testCheckGetSelectedValues()
    {
        $values = $this->getFacetResult()->getSelectedValues();
        $this->assertCount(1, $values);
        $this->assertEquals(current($values)->getDictValue(), 'test4');
    }

    public function testCheckElementCount()
    {
        $this->assertEquals($this->getFacetResult()->getElementCount(), 2);
    }

    private function getFacetResult(): FacetFilterResult
    {
        if (!is_null($this->facetResult)) {
            return $this->facetResult;
        }

        $filter = [
            'ACTIVE' => 'Y',
            '>PRICE_BASE' => 2000,
            'PROPERTY_BRAND_REF' => 'test4',
            '>PROPERTY_TEST_NUMERIC' => 10,
            '<PROPERTY_TEST_NUMERIC' => 1000,
        ];
        $facetFilter = new FacetFilter('catalog');
        return $this->facetResult = $facetFilter->getList($filter);
    }

    public static function setUpBeforeClass()
    {
        FacetFilter::fillData();
    }
}
