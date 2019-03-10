<?php

namespace Alex19pov31\Tests\BitrixFacetHelper;

use Alex19pov31\BitrixFacetHelper\FacetFilterResult;
use Alex19pov31\Tests\BitrixFacetHelper\Stubs\FacetFilter;
use PHPUnit\Framework\TestCase;

class FacetValueTest extends TestCase
{
    private $facetResult;

    public function testCheckGetFacetId()
    {
        $facetResult = $this->getFacetResult();
        $value = $facetResult->getProperty('BRAND_REF')->getFirstValue();
        $this->assertEquals($value->getFacetId(), 10);
    }
    public function testCheckGetValue()
    {
        $facetResult = $this->getFacetResult();
        $value = $facetResult->getProperty('BRAND_REF')->getFirstValue();
        $this->assertEquals($value->getValue(), 1);
    }
    public function testCheckGetDisplayValue()
    {
        $facetResult = $this->getFacetResult();
        $facetResult->getProperty('BRAND_REF')->setDisplayValue(
            [
                'test5' => 'Testing value',
            ]
        );

        $value = $facetResult->getProperty('BRAND_REF')->getFirstValue();
        $this->assertEquals($value->getDisplayValue(), 'Testing value');
    }
    public function testCheckIsSelected()
    {
        $facetResult = $this->getFacetResult();
        $values = $facetResult->getProperty('BRAND_REF')->getValues();

        foreach ($values as $value) {
            if ($value->getDictValue() == 'test4') {
                $this->assertTrue($value->isSelected());
                return;
            }
        }

        $this->assertTrue(false);
    }
    public function testCheckGetDictValue()
    {
        $facetResult = $this->getFacetResult();
        $value = $facetResult->getProperty('BRAND_REF')->getFirstValue();
        $this->assertEquals($value->getDictValue(), 'test5');
    }
    public function testCheckGetCountElement()
    {
        $facetResult = $this->getFacetResult();
        $value = $facetResult->getProperty('BRAND_REF')->getFirstValue();
        $this->assertEquals($value->getCountElement(), 1);
    }
    public function testCheckIsHide()
    {
        $facetResult = $this->getFacetResult();
        $value = $facetResult->getProperty('BRAND_REF')->getFirstValue();
        $this->assertFalse($value->isHide());
    }
    public function testCheckGetOriginCountElement()
    {
        $facetResult = $this->getFacetResult();
        $value = $facetResult->getProperty('BRAND_REF')->getFirstValue();
        $this->assertEquals($value->getOriginCountElement(), 3);
    }
    public function testCheckGetMinValue()
    {
        $facetResult = $this->getFacetResult();
        $value = $facetResult->getProperty('BRAND_REF')->getFirstValue();
        $this->assertEquals($value->getMinValue(), 0);

        $value = $facetResult->getProperty('TEST_NUMERIC')->getFirstValue();
        $this->assertEquals($value->getMinValue(), 10);
    }
    public function testCheckGetMaxValue()
    {
        $facetResult = $this->getFacetResult();
        $value = $facetResult->getProperty('BRAND_REF')->getFirstValue();
        $this->assertEquals($value->getMaxValue(), 0);

        $value = $facetResult->getProperty('TEST_NUMERIC')->getFirstValue();
        $this->assertEquals($value->getMaxValue(), 1000);
    }
    public function testCheckGetOriginMinValue()
    {
        $facetResult = $this->getFacetResult();
        $value = $facetResult->getProperty('BRAND_REF')->getFirstValue();
        $this->assertEquals($value->getOriginMinValue(), 0);

        $value = $facetResult->getProperty('TEST_NUMERIC')->getFirstValue();
        $this->assertEquals($value->getOriginMinValue(), 1);
    }
    public function testCheckGetOriginMaxValue()
    {
        $facetResult = $this->getFacetResult();
        $value = $facetResult->getProperty('BRAND_REF')->getFirstValue();
        $this->assertEquals($value->getOriginMaxValue(), 0);

        $value = $facetResult->getProperty('TEST_NUMERIC')->getFirstValue();
        $this->assertEquals($value->getOriginMaxValue(), 1200);
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

    public static function setUpBeforeClass(): void
    {
        FacetFilter::fillData();
    }
}
