<?php

namespace Alex19pov31\Tests\BitrixFacetHelper;

use Alex19pov31\Tests\BitrixFacetHelper\Stubs\FacetFilter;
use PHPUnit\Framework\TestCase;

class FacetFilterTest extends TestCase
{
    public function testCheckNormalizeFilter()
    {
        $filter = ['ACTIVE' => 'Y', 'PROPERTY_BRAND_REF' => 'test5', '>PRICE_BASE' => 2000];
        $facetFilter = new FacetFilter('catalog');
        $normalizedFilter = $facetFilter->normalizeFilter($filter);

        $this->assertEquals(['ACTIVE' => 'Y'], $normalizedFilter);
        $dictonaryFilter = array_values($facetFilter->getFacet()->dictonaryFilter);
        $priceFilter = array_values($facetFilter->getFacet()->priceFilter);

        $this->assertEquals($dictonaryFilter, [
            [
                'property_id' => 5,
                'operator' => '=',
                'value' => 1,
            ],
        ]);
        $this->assertEquals($priceFilter, [
            [
                'property_id' => 1,
                'operator' => '>=',
                'value' => 2000,
            ],
        ]);
    }

    public static function setUpBeforeClass(): void
    {
        FacetFilter::fillData();
    }
}
