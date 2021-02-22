<?php

namespace MasterDmx\ExtraAttributesPack;

use MasterDmx\LaravelExtraAttributes\Contracts\Validateable;
use MasterDmx\LaravelExtraAttributes\Attribute;

/**
 * GEO аттрибут
 * @version 1.0.0 2020-11-17
 */
class GeoAttribute extends Attribute implements Validateable
{
    /**
     * Регионы
     *
     * @var array
     */
    public $regions;

    /**
     * Города
     *
     * @var array
     */
    public $cities;

    // --------------------------------------------------------
    // Helpers
    // --------------------------------------------------------

    private function compareArrays(array $first, array $second)
    {
        if (empty($first)) {
            return empty($second);
        }

        if (empty($second)) {
            return false;
        }

        $second = array_flip($second);

        foreach (array_keys(array_flip($first)) as $key) {
            if (isset($second[$key])) {
                return true;
            }
        }

        return false;
    }

    // --------------------------------------------------------
    // Base
    // --------------------------------------------------------

    /**
     * Сравнение с другим полем
     *
     * @return bool
     */
    public function compare($attribute): bool
    {
        return isset($this->regions) && $this->compareArrays($this->regions, $attribute->regions ?? []) || isset($this->cities) && $this->compareArrays($this->cities, $attribute->cities ?? []);
    }

    public function isValidRaw($data): bool
    {
        return true;
    }

    public function isValid(): bool
    {
        return !empty($this->regions) || !empty($this->cities);
    }

    public function importRaw($data): void
    {
        $this->import($data);
    }

    /**
     * Импорт значений
     *
     * @param array|int|string|double|float $data
     * @return void
     */
    public function import($data): void
    {
        parent::import($data);

        if (isset($data['regions'])) {
            if (is_array($data['regions'])) {
                $this->regions = $data['regions'];
            } else {
                $this->regions = explode(',', $data['regions']);
            }
        }

        if (isset($data['cities'])) {
            if (is_array($data['cities'])) {
                $this->cities = $data['cities'];
            } else {
                $this->cities = explode(',', $data['cities']);
            }
        }
    }

    /**
     * Экспорт значений
     *
     * @return array|int|string|double|float
     */
    public function export()
    {
        return parent::export() + [
            'regions' => $this->regions,
            'cities' => $this->cities,
        ];
    }

    /**
     * Проверка на пустоту хранящихся значений
     *
     * @return bool
     */
    public function checkForEmpty(): bool
    {
        return !empty($this->regions) || !empty($this->cities);
    }
}
