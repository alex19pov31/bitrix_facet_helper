<?php

namespace Alex19pov31\Tests\BitrixFacetHelper\Stubs\Bitrix;

class Dictionary
{
    private static $data;

    public function getStringId($value): int
    {
        return !empty(static::$data[$value]) ? (int) static::$data[$value] : 0;
    }

    public function getStringById(int $id): string
    {
        foreach (static::$data as $value => $idValue) {
            if ($idValue == $id) {
                return (string) $value;
            }
        }

        return '';
    }

    public static function setData(array $data): void
    {
        static::$data = $data;
    }
}
