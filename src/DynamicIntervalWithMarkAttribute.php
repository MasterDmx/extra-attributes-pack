<?php

namespace MasterDmx\ExtraAttributesPack;

use MasterDmx\LaravelExtraAttributes\Contracts\Validateable;
use MasterDmx\LaravelHelpers\NumericHelper;
use MasterDmx\LaravelHelpers\StringHelper;

/**
 * Интервал с динамическими единицами измерения
 * @version 1.0.0 2021-02-17
 */
class DynamicIntervalWithMarkAttribute extends DynamicIntervalAttribute
{
    /**
     * Пометка
     *
     * @var string
     */
    public $mark;

    // --------------------------------------------------------
    // Base
    // --------------------------------------------------------

    public function import($data): void
    {
        parent::import($data);

        $this->mark = $data['mark'] ?? null;
    }

    public function export(): array
    {
        return parent::export() + array_filter([
            'mark' => $this->mark,
        ], function ($el) {
            return isset($el);
        });
    }

    public function isValid(): bool
    {
        return parent::isValid() || !empty($this->mark);
    }
}
