<?php

namespace Alex19pov31\Tests\BitrixFacetHelper\Stubs;

trait FacetTrait
{
    protected function getIblockIdByCode(string $code, int $ttl = 86400): int
    {
        return 2;
    }

    /**
     * Get storage object
     *
     * @return Bitrix\Storage
     */
    protected static function getStorageObject()
    {
        return new Bitrix\Storage;
    }

    /**
     * Get group table object
     *
     * @return Bitrix\GroupTable
     */
    protected static function getGroupTableObject()
    {
        return new Bitrix\GroupTable;
    }

    /**
     * Get facet property object
     *
     * @param array $prop
     * @return FacetProperty
     */
    protected static function getFacetPropertyObject(array $prop = [])
    {
        return new FacetProperty($prop);
    }

    public static function includeModule(string $moduleName): bool
    {
        return true;
    }
}
