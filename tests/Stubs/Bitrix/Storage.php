<?php

namespace Alex19pov31\Tests\BitrixFacetHelper\Stubs\Bitrix;

class Storage
{
    /**
     * Return facet id for price
     *
     * @param integer $priceId
     * @return integer
     */
    public static function priceIdToFacetId(int $priceId): int
    {
        return intval($priceId * 2 + 1);
    }

    /**
     * Return facet id for property
     *
     * @param integer $propertyId
     * @return integer
     */
    public static function propertyIdToFacetId(int $propertyId): int
    {
        return intval($propertyId * 2);
    }
}
