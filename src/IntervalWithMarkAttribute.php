<?php

namespace MasterDmx\ExtraAttributesPack;

/**
 * Интервал
 * @version 1.0.1 2020-11-17
 */
class IntervalWithMarkAttribute extends IntervalAttribute
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

    public function export()
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
