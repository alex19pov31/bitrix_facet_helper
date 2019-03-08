<?php

namespace Alex19pov31\BitrixFacetHelper;

class FacetValue extends BaseModel
{
    public function __construct(array $value)
    {
        $this->fields = [
            'FACET_ID' => $value['FACET_ID'],
            'VALUE' => $value['VALUE'],
            'MIN_VALUE_NUM' => $value['MIN_VALUE_NUM'],
            'MAX_VALUE_NUM' => $value['MAX_VALUE_NUM'],
            'VALUE_FRAC_LEN' => $value['VALUE_FRAC_LEN'],
            'ELEMENT_COUNT' => $value['ELEMENT_COUNT'],
            'DICTIONARY_VALUE' => $value['DICTIONARY_VALUE'],
            'SORT' => !empty($value['SORT']) ? (int) $value['SORT'] : 0,
            'SELECTED' => !empty($value['SELECTED']) ? (bool) $value['SELECTED'] : false,
        ];

        $this->original = [
            'MIN_VALUE_NUM' => $value['ORIGIN_MIN_VALUE_NUM'],
            'MAX_VALUE_NUM' => $value['ORIGIN_MAX_VALUE_NUM'],
            'ELEMENT_COUNT' => $value['ORIGIN_ELEMENT_COUNT'],
        ];
    }

    public function getData(): array
    {
        $data = $this->fields;
        $data['ORIGIN_MIN_VALUE_NUM'] = $this->original['MIN_VALUE_NUM'];
        $data['ORIGIN_MAX_VALUE_NUM'] = $this->original['MAX_VALUE_NUM'];
        $data['ORIGIN_ELEMENT_COUNT'] = $this->original['ELEMENT_COUNT'];

        return $data;
    }

    /**
     * Идентифкатор свойства в индексе
     *
     * @return integer
     */
    public function getFacetId(): int
    {
        return (int) $this['FACET_ID'];
    }

    /**
     * Идентификатор свойства в индексе
     *
     * @return string
     */
    public function getValue(): string
    {
        return (string) $this['VALUE'];
    }

    /**
     * Значение для отображения
     *
     * @return string
     */
    public function getDisplayValue(): string
    {
        return (string) $this['DISPLAY_VALUE'];
    }

    public function isSelected(): bool
    {
        $cMinValue = (float) $this->fields["MIN_VALUE_NUM"];
        $cMaxValue = (float) $this->fields["MAX_VALUE_NUM"];

        $oMinValue = (float) $this->original["MIN_VALUE_NUM"];
        $oMaxValue = (float) $this->original["MAX_VALUE_NUM"];

        if ($cMinValue != $oMinValue || $cMaxValue != $oMaxValue) {
            return true;
        }

        return (bool) $this['SELECTED'];
    }

    /**
     * Значение из словаря
     *
     * @return string
     */
    public function getDictValue(): string
    {
        return (string) $this['DICTIONARY_VALUE'];
    }

    /**
     * Количество найденых элементов
     *
     * @return integer
     */
    public function getCountElement(): int
    {
        return (int) $this['ELEMENT_COUNT'];
    }

    public function isHide(): bool
    {
        return (int) $this['ELEMENT_COUNT'] <= 0;
    }

    public function showDataIsHidden(string $data): string
    {
        return $this->isHide() ? $data : "";
    }

    /**
     * Изначальное количество элементов
     *
     * @return integer
     */
    public function getOriginCountElement(): int
    {
        return (int) $this->original['ELEMENT_COUNT'];
    }

    /**
     * Минимальное значение (только для числового типа свойства)
     *
     * @return float
     */
    public function getMinValue(): float
    {
        return (float) $this['MIN_VALUE_NUM'];
    }

    /**
     * Максимальное значение (только для числового типа свойства)
     *
     * @return float
     */
    public function getMaxValue(): float
    {
        return (float) $this['MAX_VALUE_NUM'];
    }

    public function getOriginMinValue(): float
    {
        return (float) $this->original['MIN_VALUE_NUM'];
    }

    public function getOriginMaxValue(): float
    {
        return (float) $this->original['MAX_VALUE_NUM'];
    }

    /**
     * Активность
     *
     * @return boolean
     */
    public function isActive(): bool
    {
        return $this->getCountElement() > 0;
    }
}
