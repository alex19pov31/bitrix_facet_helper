<?php

namespace Alex19pov31\Tests\BitrixFacetHelper\Stubs\Bitrix\DB;

use ArrayIterator;

class Result
{
    private $data = null;
    private static $namedDataSet;

    public function __construct(string $name)
    {
        $this->data = new ArrayIterator((array) static::$namedDataSet[$name]);
    }

    /**
     * Return data iterator
     *
     * @return ArrayIterator
     */
    private function getData()
    {
        return $this->data;
    }

    /**
     * Fetch data
     *
     * @return null|array
     */
    public function fetch()
    {
        if (!$this->getData()->valid()) {
            return null;
        }

        $item = $this->getData()->current();
        $this->getData()->next();

        return (array) $item;
    }

    public static function setData(string $name, array $data)
    {
        static::$namedDataSet[$name] = $data;
    }
}
