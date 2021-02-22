<?php

namespace MasterDmx\ExtraAttributesPack;

use Illuminate\Support\Arr;
use MasterDmx\LaravelExtraAttributes\Contracts\Validateable;
use MasterDmx\LaravelExtraAttributes\Attribute;

/**
 * Аттрибут списка телефонных номеров
 *
 */
class PhonesAttribute extends Attribute implements Validateable
{
    const KEY_NUMBERS = 'numbers';
    const KEY_MARKS = 'marks';

    /**
     * Телефонные номера
     *
     * @var array|null
     */
    public $numbers;

    /**
     * Пометки
     *
     * @var array|null
     */
    public $marks;

    // --------------------------------------------------------
    // Base
    // --------------------------------------------------------

    public function isValidRaw($data): bool
    {
        return true;
    }

    public function isValid(): bool
    {
        return !empty($this->numbers);
    }

    public function importRaw($data): void
    {
        if (!empty($data[static::KEY_NUMBERS])) {
            $this->numbers = array_filter($data[static::KEY_NUMBERS], fn ($el) => !empty($el));
        }

        if (!empty($data[static::KEY_MARKS])) {
            foreach ($data[static::KEY_MARKS] as $key => $value) {
                if (!empty($value) && isset($this->numbers[$key])) {
                    $this->marks[$key] = $value;
                }
            }
        }
    }

    public function import($data): void
    {
        if (!empty($data[static::KEY_NUMBERS])) {
            $this->numbers = $data[static::KEY_NUMBERS];
        }

        if (!empty($data[static::KEY_MARKS])) {
            $this->marks = $data[static::KEY_MARKS];
        }
    }

    public function export()
    {
        return array_filter([
            'numbers' => $this->numbers,
            'marks' => $this->marks,
        ], fn ($el) => isset($el));
    }
}
