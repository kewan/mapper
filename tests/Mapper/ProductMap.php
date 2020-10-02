<?php


namespace Tests\Kewan\Mapper;

use Kewan\Mapper\Mappable;

class ProductMap extends Mappable
{
    public function getMap(): array
    {
        return [
            'id'    => 'Item.Id',
            'name'  => 'Item.Name',
            'title' => function ($data) {
                return self::getTitle($data);
            },
            'price' => 'Item.Price',
        ];
    }

    public function getDefaults(): array
    {
        return [
            'price'    => 1000.99,
            'discount' => '10%',
        ];
    }

    public function getTitle($data)
    {

        return 'This is the title for: ' . $data->Item->Name;

    }
}