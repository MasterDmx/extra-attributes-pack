<?php

namespace MasterDmx\ExtraAttributesPack;

use MasterDmx\LaravelExtraAttributes\Attribute;

class SiteAttribute extends Attribute
{
    /**
     * Url
     *
     * @var string
     */
    public $url;

    /**
     * Ancor
     *
     * @var string
     */
    public $ancor;

    // --------------------------------------------------------
    // Base
    // --------------------------------------------------------

    /**
     * Импорт значений из массива
     *
     * @param array|int|string|double|float $data
     * @return void
     */
    public function import($data): void
    {
        parent::import($data);

        $this->url = $data['url'] ?? null;
        $this->ancor = $data['ancor'] ?? null;
    }

    /**
     * Экспорт значений
     *
     * @return array|int|string|double|float
     */
    public function export()
    {
        return parent::export() + array_filter([
            'url' => $this->url,
            'ancor' => $this->ancor,
        ], function ($el) {
            return isset($el);
        });
    }

    public function checkForEmpty(): bool
    {
        return !empty($this->url);
    }
}
