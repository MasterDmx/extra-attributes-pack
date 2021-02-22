<?php

namespace MasterDmx\ExtraAttributesPack;

use MasterDmx\LaravelExtraAttributes\Contracts\Validateable;
use MasterDmx\LaravelHelpers\NumericHelper;
use MasterDmx\LaravelHelpers\StringHelper;

/**
 * Интервал с динамическими единицами измерения
 * @version 1.0.1 2020-11-17
 */
class DynamicIntervalAttribute extends IntervalAttribute implements Validateable
{
    /**
     * Единица измерения минимального значения
     *
     * @var string
     */
    public $minUnit;

    /**
     * Единица измерения максимального значения
     *
     * @var string
     */
    public $maxUnit;

    /**
     * Единица измерения по умолчанию
     *
     * @var string
     */
    public $defaultUnit;

    /**
     * Единицы измерения
     *
     * @var array
     */
    public $units;

    // --------------------------------------------------------
    // Helpers
    // --------------------------------------------------------

    /**
     * Трансформирует число по алгоритму в строке
     * @param mixed $numeric Число для трансформирования
     * @param string $logic Алгоритм преобразования, например: *2|+10|-1|/42 (умножить на 2, потом прибавить 10, потом отнять 1 и поделить на 42)
     * @param bool $inversion Произвести все в обратном порядке
     */
    protected function transformate($numeric, string $logic, bool $inversion = false)
    {
        return NumericHelper::transformate($numeric, $logic, $inversion);
    }

    // ----------------------------------------------------------------
    // Проверки
    // ----------------------------------------------------------------

    /**
     * Единицы измерения равны
     * @return bool
     */
    public function isEqualUnits() : bool
    {
        return $this->minUnit == $this->maxUnit;
    }

    // --------------------------------------------------------
    // View
    // --------------------------------------------------------

    /**
     * Получить трансформирвоанное значение
     *
     * @param string $valuePrefix
     * @param string $unit
     */
    // protected function getTransformatedValue($valuePrefix = 'min', string $unit)
    // {
    //     return $this->transformate($this->$valuePrefix, $this->units[$unit]['transformation'] ?? '');
    // }

    /**
     * Вывод единицы измерения
     */
    public function showUnit(string $key, bool $genitiveIncline = false)
    {
        $unitKey = $key . 'Unit';

        if (!isset($this->units[$this->$unitKey][$genitiveIncline ? 'inclineGenitive' : 'incline'])) {
            return isset($this->units[$this->$unitKey]['name']) ? $this->units[$this->$unitKey]['name'] : '';
        }

        return StringHelper::inclineByNumericOfArray($this->getValue($key), $this->units[$this->$unitKey][$genitiveIncline ? 'inclineGenitive' : 'incline']);
    }

    private function selectPattern()
    {
        foreach (array_keys($this->patterns) ?? [] as $action) {
            if (
                $action == 'equal' && $this->isEqual() ||
                $action == 'interval' && $this->isInterval() ||
                $action == 'interval_with_units_equal' && $this->isInterval() && $this->isEqualUnits() ||
                $action == 'min' && isset($this->min) ||
                $action == 'max' && isset($this->max)
            ) {
                return $action;
            }
        }

        return null;
    }

    private function replacePatternTags(string $str)
    {
        if (!(strpos($str, '{value}') === false)) {
            $str = str_replace('{value}', $this->showValue('min'), $str);
        }

        if (!(strpos($str, '{unit}') === false)) {
            $str = str_replace('{unit}', $this->showUnit('min'), $str);
        }

        if (!(strpos($str, '{unitGenitive}') === false)) {
            $str = str_replace('{unitGenitive}', $this->showUnit('min', true), $str);
        }

        if (!(strpos($str, '{min}') === false)) {
            $str = str_replace('{min}', $this->showValue('min'), $str);
        }

        if (!(strpos($str, '{max}') === false)) {
            $str = str_replace('{max}', $this->showValue('max'), $str);
        }

        if (!(strpos($str, '{minUnit}') === false)) {
            $str = str_replace('{minUnit}', $this->showUnit('min'), $str);
        }

        if (!(strpos($str, '{minUnitGenitive}') === false)) {
            $str = str_replace('{minUnitGenitive}', $this->showUnit('min', true), $str);
        }

        if (!(strpos($str, '{maxUnit}') === false)) {
            $str = str_replace('{maxUnit}', $this->showUnit('max'), $str);
        }

        if (!(strpos($str, '{maxUnitGenitive}') === false)) {
            $str = str_replace('{maxUnitGenitive}', $this->showUnit('max', true), $str);
        }

        return $str;
    }

    // --------------------------------------------------------
    // Base
    // --------------------------------------------------------

    protected function init(array $properties): void
    {
        parent::init($properties);
        $this->units = $properties['units'] ?? null;
        $this->defaultUnit = $properties['defaultUnit'] ?? null;
    }

    public function import($data): void
    {
        parent::import($data);

        $this->minUnit = $data['minUnit'] ?? null;
        $this->maxUnit = $data['maxUnit'] ?? null;

        if (isset($data['minRaw']) && $this->minUnit !== $this->defaultUnit) {
            $this->min = $this->transformate($this->min, $this->units[$this->minUnit]['transformation'] ?? '', true);
        }

        if (isset($data['maxRaw']) && $this->maxUnit !== $this->defaultUnit) {
            $this->max = $this->transformate($this->max, $this->units[$this->maxUnit]['transformation'] ?? '', true);
        }
    }

    public function export(): array
    {
        return parent::export() + array_filter([
            'minUnit' => $this->minUnit,
            'maxUnit' => $this->maxUnit,
        ], function ($el) {
            return isset($el);
        });
    }
}
