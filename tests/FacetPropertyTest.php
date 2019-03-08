<?php

namespace Alex19pov31\Tests\BitrixFacetHelper;

use Alex19pov31\Tests\BitrixFacetHelper\Stubs\FacetFilter;
use PHPUnit\Framework\TestCase;
use Alex19pov31\BitrixFacetHelper\FacetFilterResult;
use Alex19pov31\Tests\BitrixFacetHelper\Stubs\FacetProperty;

class FacetPropertyTest extends TestCase
{
    private $facetResult;

    public function testCheckGetID()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->getID(), 5);
    }

    public function testCheckGetFacetID()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->getFacetID(), 10);
    }

    public function testCheckGetName()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->getName(), 'Бренд');
    }

    public function testCheckGetCode()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->getCode(), 'BRAND_REF');
    }

    public function testCheckGetType()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->GetType(), 'S');
    }

    public function testCheckGetUserType()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->GetUserType(), 'directory');
    }

    public function testCheckGetHint()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->getHint(), 'Test Hint');
    }

    public function testCheckIsActive()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->isActive(), true);
    }

    public function testCheckIsMultiple()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->isMultiple(), true);
    }

    public function testCheckIsRequired()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->isRequired(), false);
    }

    public function testCheckIsEmptyUserType()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->isEmptyUserType(), false);
    }

    public function testCheckIsNumericProperty()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->isNumericProperty(), false);
    }

    public function testCheckIsDatetimeProperty()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->isDatetimeProperty(), false);
    }

    public function testCheckIsPriceProperty()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->isPriceProperty(), false);
    }

    public function testCheckIsDictionaryProperty()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->isDictionaryProperty(), true);
    }

    public function testCheckGetMinValue()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->getMinValue(), 0);
    }

    public function testCheckIsValidValues()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->isValidValues(), true);
    }

    public function testCheckGetValues()
    {
        $facetResult = $this->getFacetResult();
        $values = $facetResult->getProperty('BRAND_REF')->getValues();
        $this->assertCount(5, $values);
    }

    public function testCheckGetMaxValue()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->getMaxValue(), 0);
    }

    public function testCheckGetOriginMinValue()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->getOriginMinValue(), 0);
    }

    public function testCheckGetOriginMaxValue()
    {
        $facetResult = $this->getFacetResult();
        $this->assertEquals($facetResult->getProperty('BRAND_REF')->getOriginMaxValue(), 0);
    }

    public function testCheckGetPropertyCode()
    {
        $operation = FacetProperty::getPropertyCode('>PROPERTY_BRAND_REF');
        $this->assertEquals($operation, 'BRAND_REF');

        $operation = FacetProperty::getPropertyCode('>=PROPERTY_BRAND_REF');
        $this->assertEquals($operation, 'BRAND_REF');

        $operation = FacetProperty::getPropertyCode('<PROPERTY_BRAND_REF');
        $this->assertEquals($operation, 'BRAND_REF');

        $operation = FacetProperty::getPropertyCode('<=PROPERTY_BRAND_REF');
        $this->assertEquals($operation, 'BRAND_REF');

        $operation = FacetProperty::getPropertyCode('=PROPERTY_BRAND_REF');
        $this->assertEquals($operation, 'BRAND_REF');

        $operation = FacetProperty::getPropertyCode('PROPERTY_BRAND_REF');
        $this->assertEquals($operation, 'BRAND_REF');
    }

    public function testCheckGetPropertyOperation()
    {
        $operation = FacetProperty::getPropertyOperation('>PROPERTY_BRAND_REF');
        $this->assertEquals($operation, '>=');

        $operation = FacetProperty::getPropertyOperation('>=PROPERTY_BRAND_REF');
        $this->assertEquals($operation, '>=');

        $operation = FacetProperty::getPropertyOperation('<PROPERTY_BRAND_REF');
        $this->assertEquals($operation, '<=');

        $operation = FacetProperty::getPropertyOperation('<=PROPERTY_BRAND_REF');
        $this->assertEquals($operation, '<=');

        $operation = FacetProperty::getPropertyOperation('=PROPERTY_BRAND_REF');
        $this->assertEquals($operation, '=');

        $operation = FacetProperty::getPropertyOperation('PROPERTY_BRAND_REF');
        $this->assertEquals($operation, '=');
    }

    public function testCheckSortValues()
    {
        $facetResult = $this->getFacetResult();
        $property = $facetResult->getProperty('BRAND_REF')->sortValues('DICTIONARY_VALUE');
        
        $index = 1;
        foreach($property->getValues() as $value) {
            $this->assertEquals($value->getDictValue(), 'test'.$index++);
        }
    }

    public function testCheckSortValuesByData()
    {
        $facetResult = $this->getFacetResult();

        $sortData = [
            'test1' => 1,
            'test4' => 2,
            'test3' => 3,
            'test5' => 4,
            'test2' => 5,
        ];

        $sortKeys = array_keys($sortData);
        $property = $facetResult->getProperty('BRAND_REF')->sortValuesByData($sortData, 'DICTIONARY_VALUE');
        $index = 0;
        foreach($property->getValues() as $value) {
            $this->assertEquals($value->getDictValue(), $sortKeys[$index++]);
        }
    }

    public function testCheckSelectedValue()
    {
        $facetResult = $this->getFacetResult();
        $property = $facetResult->getProperty('BRAND_REF');

        foreach($property->getValues() as $value) {
            if ($value->getDictValue() == 'test4') {
                $this->assertEquals($value->isSelected(), true);
                return;
            }
        }
        $this->assertTrue(false);
    }

    /**
     * @depends testCheckSortValues
     * @depends testCheckSortValuesByData
     */
    public function testCheckGetFirstValue()
    {
        $facetResult = $this->getFacetResult();
        $property = $facetResult->getProperty('BRAND_REF');
        $this->assertEquals($property->getFirstValue()->getDictValue(), 'test5');

        $property = $property->sortValues('DICTIONARY_VALUE');
        $this->assertEquals($property->getFirstValue()->getDictValue(), 'test1');

        $sortData = [
            'test3' => 1,
            'test4' => 2,
            'test1' => 3,
            'test5' => 4,
            'test2' => 5,
        ];
        $property = $facetResult->getProperty('BRAND_REF')->sortValuesByData($sortData, 'DICTIONARY_VALUE');
        $this->assertEquals($property->getFirstValue()->getDictValue(), 'test3');
    }

    private function getFacetResult(): FacetFilterResult
    {
        if (!is_null($this->facetResult)) {
            return $this->facetResult;
        }

        $filter = ['ACTIVE' => 'Y', '>PRICE_BASE' => 2000, 'PROPERTY_BRAND_REF' => 'test4'];
        $facetFilter = new FacetFilter('catalog');
        return $this->facetResult = $facetFilter->getList($filter);
    }

    public static function setUpBeforeClass(): void
    {
        FacetFilter::fillData();
    }
}
