<?php

namespace MasterDmx\ExtraAttributesPack;

use MasterDmx\LaravelExtraAttributes\Entities\Attribute;
use MasterDmx\LaravelHelpers\ArrayHelper;

/**
 * Список
 * @version 1.0.1 2020-11-17
 */
class ListAttribute extends Attribute
{
    const KEY_VALUES = 'values';
    const KEY_VALUES_MARKS = 'values_marks';
    const KEY_MARK = 'mark';

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

    /**
     * Пометка
     *
     * @var string|null
     */
    public $mark;

    /**
     * Пометки для значений
     *
     * @var array|null
     */
    public $valueMarks;

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

    /**
     * Сравнение с другим полем
     *
     * @return bool
     */
    public function compare($attribute): bool
    {
        return ArrayHelper::compare($this->values, $attribute->values);
    }

    /**
     * Инициализация прототипа
     *
     * @param array $properties
     * @return void
     */
    public function init(array $properties)
    {
        parent::init($properties);
        $this->handbook = $properties['handbook'];
    }

    /**
     * Импорт значений из массива
     *
     * @param array|int|string|double|float $data
     * @return void
     */
    public function import($data): void
    {
        parent::import($data);

        $this->values = $data[static::KEY_VALUES] ?? [];
        $this->mark = $data[static::KEY_MARK] ?? null;
        $this->valueMarks = $data[static::KEY_VALUES_MARKS] ?? null;
    }

    /**
     * Экспорт значений
     *
     * @return array|int|string|double|float
     */
    public function export()
    {
        return parent::export() + array_filter([
            static::KEY_VALUES => $this->values,
            static::KEY_VALUES_MARKS => $this->valueMarks,
            static::KEY_MARK => $this->mark,
        ], function ($el) {
            return isset($el);
        });
    }

    /**
     * Изменить данные по пресету
     *
     * @param $data
     * @return self
     */
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
