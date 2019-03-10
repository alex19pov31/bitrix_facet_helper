[![Build Status](https://api.travis-ci.org/alex19pov31/bitrix_facet_helper.svg?branch=master)](https://travis-ci.org/alex19pov31/bitrix_facet_helper)

# Bitrix facet helper

Хелпер для работы с фасетными индексами.

### Установка

```composer require alex19pov31/bitrix_facet_helper```

### Использование

```php
<?php

use Alex19pov31\BitrixFacetHelper\FacetFilter;

$facetResult = (new FacetFilter('catalog'))
    ->getList([
        'ACTIVE' => 'Y',
        'PROPERTY_COLOR' => 'brown',
    ]);

$facetResult->getValues();
// Количество элементов после фильтрации
$facetResult->getElementCount();
// Список значений свойства
$facetResult->getProperty('COLOR')->getValues();
// Список значений элементов справочника
$facetResult->getProperty('COLOR')->getDictValues();
//$facetResult['COLOR']->getDictValues();

$facetResult->getProperty('PRICE_BASE')->getMinValue();
$facetResult->getProperty('PRICE_BASE')->getMaxValue();
$facetResult->getProperty('PRICE_BASE')->getOriginMinValue();
$facetResult->getProperty('PRICE_BASE')->getOriginMaxValue();
$facetResult->getProperty('PRICE_BASE')->isValidValues();

```