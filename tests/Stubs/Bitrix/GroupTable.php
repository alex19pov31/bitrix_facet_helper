<?php

namespace Alex19pov31\Tests\BitrixFacetHelper\Stubs\Bitrix;

class GroupTable
{
    public static function getList(): DB\Result
    {
        return new DB\Result('group_table');
    }
}
