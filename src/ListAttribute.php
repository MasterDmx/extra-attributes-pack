<?php

namespace MasterDmx\ExtraAttributesPack;

use Illuminate\Support\Arr;
use MasterDmx\LaravelExtension\Helpers\ArrayHelper;
use MasterDmx\LaravelExtraAttributes\Contracts\Validateable;
use MasterDmx\LaravelExtraAttributes\Attribute;

/**
 * Список
 * @version 1.0.1 2020-11-17
 */
class ListAttribute extends Attribute implements Validateable
{
    const KEY_VALUES = 'values';

    /**
     * Значения
     *
     * @var array|null
     */
    public $values = [];

    /**
     * Все значения
     *
     * @var array|null
     */
    public $handbook;

    // --------------------------------------------------------
    // Functional
    // --------------------------------------------------------

    /**
     * Проверить наличие значения по ключу
     *
     * @param [type] $item
     * @return boolean
     */
    public function has($item): bool
    {
        return in_array($item, $this->values);
    }

    /**
     * Проверить наличие значения по ключу
     *
     * @param [type] $item
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return empty($this->values);
    }

    // --------------------------------------------------------
    // View
    // --------------------------------------------------------

    /**
     * Вывести выделенные значения по паттерну
     */
    public function show(string $pattern)
    {
        $content = '';

        foreach ($this->values ?? [] as $key) {
            if (isset($this->handbook[$key])) {
                $content .= $this->replacePatternTags($pattern, $this->handbook[$key]);
            }
        }

        return $content;
    }

    /**
     * Замена тегов в паттернах
     */
    private function replacePatternTags(string $str, string $value)
    {
        if (!(strpos($str, '{value}') === false)) {
            $str = str_replace('{value}', $value, $str);
        }

        return $str;
    }

    // --------------------------------------------------------
    // Base
    // --------------------------------------------------------

    public function compare($attribute): bool
    {
        return ArrayHelper::compare($this->values, $attribute->values);
    }

    public function init(array $properties)
    {
        parent::init($properties);
        $this->handbook = $properties['handbook'];
    }

    public function isValidRaw($data): bool
    {
        return true;
    }

    public function isValid(): bool
    {
        return !empty($this->values);
    }

    public function importRaw($data): void
    {
        if (!empty($data[static::KEY_VALUES])) {
            $this->values = Arr::where($data[static::KEY_VALUES], fn ($el) => isset($el));
        }
    }

    public function import($data): void
    {
        $this->values     = $data[static::KEY_VALUES] ?? null;
    }

    public function export()
    {
        return parent::export() + array_filter([
            static::KEY_VALUES => $this->values,
        ], function ($el) {
            return isset($el);
        });
    }


    protected function changeUnderPreset(array $data): void
    {
        parent::changeUnderPreset($data);

        if (isset($data['intersect'])) {
            foreach ($this->handbook as $key => $value) {
                if (!in_array($key, $data['intersect'])) {
                    unset($this->handbook[$key]);
                }
            }
        }

        if (isset($data['exclude'])) {
            foreach ($data['exclude'] as $key) {
                if (isset($this->handbook[$key])) {
                    unset($this->handbook[$key]);
                }
            }
        }
    }
}
