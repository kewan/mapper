<?php

namespace Tests\Kewan\Mapper;

class DemoProduct
{
    public $sku = '12345';

    public function getSku()
    {
        return $this->sku;
    }

    public function getTitle()
    {
        return "An amazing demo product";
    }
}