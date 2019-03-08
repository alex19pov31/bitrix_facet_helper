<?php

namespace Alex19pov31\Tests\BitrixFacetHelper\Stubs;

use Alex19pov31\BitrixFacetHelper\FacetProperty as FacetPropertyOrigin;
use Alex19pov31\Tests\BitrixFacetHelper\Stubs\Bitrix\DB\Result;

class FacetProperty extends FacetPropertyOrigin
{
    use FacetTrait;

    const TYPE_STRING = 'S';
    const TYPE_NUMBER = 'N';

    protected static function query(int $iblockId, array $filter = [])
    {
        return new Result('property_query');
    }

    public function isNumericProperty(): bool
    {
        return static::TYPE_NUMBER == $this->getType()
        && $this->isEmptyUserType();
    }

    public function isDatetimeProperty(): bool
    {
        return static::TYPE_STRING == $this->getType()
        && $this->getUserType() == 'DateTime';
    }

}
