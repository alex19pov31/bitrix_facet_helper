<?php

namespace Alex19pov31\BitrixFacetHelper;

use Alex19pov31\BitrixFacetHelper\FacetProperty;
use Bitrix\Catalog\GroupTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyIndex\Storage;
use Bitrix\Main\Loader;

trait FacetTrait
{
    private static $iblockList = [];
    private static $storageObject;
    private static $groupTableObject;
    private static $facetPropertyObject;

    protected function getIblockIdByCode(string $code, int $ttl = 86400): int
    {
        if (!empty(static::$iblockList[$code]) && (int) static::$iblockList[$code] > 0) {
            return (int) static::$iblockList[$code];
        }

        $data = (array) IblockTable::getList([
            'select' => ['ID', 'CODE'],
            'cache' => ['ttl' => $ttl],
        ])->fetchAll();
        static::$iblockList = collect($data)->pluck('ID', 'CODE')->toArray();

        return (int) static::$iblockList[$code];
    }

    /**
     * Get storage object
     *
     * @param integer $iblockId
     * @return void
     */
    protected static function getStorageObject(int $iblockId)
    {
        return new Storage($iblockId);
    }

    /**
     * Get group table object
     *
     * @return GroupTable
     */
    protected static function getGroupTableObject()
    {
        return new GroupTable;
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
        return (bool) Loader::IncludeModule($moduleName);
    }
}
