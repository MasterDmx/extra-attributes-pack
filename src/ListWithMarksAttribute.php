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
class ListWithMarksAttribute extends ListAttribute
{
    const KEY_VALUES_MARKS = 'values_marks';
    const KEY_MARK = 'mark';

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

    public function isValid(): bool
    {
        return parent::isValid() || !empty($this->valueMarks);
    }

    public function importRaw($data): void
    {
        parent::importRaw($data);

        if (!empty($data[static::KEY_VALUES_MARKS])) {
            $this->valueMarks = Arr::where($data[static::KEY_VALUES_MARKS], fn ($el) => isset($el));
        }

        $this->mark = $data[static::KEY_MARK] ?? null;
    }

    public function import($data): void
    {
        parent::import($data);

        $this->valueMarks = $data[static::KEY_VALUES_MARKS] ?? null;
        $this->mark       = $data[static::KEY_MARK] ?? null;
    }

    public function export()
    {
        return parent::export() + array_filter([
            static::KEY_VALUES_MARKS => $this->valueMarks,
            static::KEY_MARK         => $this->mark,
        ], function ($el) {
            return isset($el);
        });
    }
}
