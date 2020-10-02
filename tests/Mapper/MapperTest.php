<?php

namespace Tests\Kewan\Mapper;

use Kewan\Mapper\Mapper;
use PHPUnit\Framework\TestCase;

class Item
{
    private $name;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}

class SourceItem
{
    private $listId = 'list-123';

    public function getListId()
    {
        return $this->listId;
    }
}

class MapperTest extends TestCase
{

    /**
     * @test
     */
    public function it_can_map_from_array()
    {
        $foreignData = [
            'Response' => [
                'Item' => [
                    'Id'   => 1234,
                    'Name' => 'iPhone 8',
                ],
            ],
        ];

        $product = new \stdClass();

        $mapper  = new Mapper();
        $product = $mapper->from($foreignData, [
            'Response.Item.Id',
            'Response.Item.Name',
            'Response.Non.Existent',
        ])->to($product, [
            'id',
            'name',
            'title',
        ])->withDefaults([
            'title' => 'Monkey',
        ])->get();

        $this->assertEquals(1234, $product->id);
        $this->assertEquals('iPhone 8', $product->name);
        $this->assertEquals('Monkey', $product->title);
    }

    /**
     * @test
     */
    public function it_can_map_from_object()
    {
        $response             = new \stdClass();
        $response->Item       = new \stdClass();
        $response->Item->Id   = 1234;
        $response->Item->Name = "iPhone 8";

        $product = new \stdClass();

        $mapper = new Mapper();

        $product = $mapper->from($response, ['Item.Id', 'Item.Name'])->to($product, ['id', 'name'])->get();

        $this->assertEquals(1234, $product->id);
        $this->assertEquals('iPhone 8', $product->name);
    }

    /**
     * @test
     */
    public function it_can_map_into_children()
    {
        $response                 = new \stdClass();
        $response->Item           = new \stdClass();
        $response->Item->Id       = 1234;
        $response->Item->Name     = "iPhone 8";
        $response->Item->Category = "Phones";

        $product                 = new \stdClass();
        $product->category       = new \stdClass();
        $product->category->name = '';

        $mapper = new Mapper([
            'id'            => 'Item.Id',
            'name'          => 'Item.Name',
            'category.name' => 'Item.Category',
        ]);

        $product = $mapper->from($response)->to($product)->get();

        $this->assertEquals(1234, $product->id);
        $this->assertEquals('iPhone 8', $product->name);
        $this->assertEquals('Phones', $product->category->name);
    }

    /**
     * @test
     */
    public function it_can_map_using_objects_method()
    {
        $foreignData = [
            'Response' => [
                'Item' => [
                    'Id'   => 1234,
                    'Name' => 'iPhone 8',
                ],
            ],
        ];

        $item = new Item();

        $mapper = new Mapper();
        $item = $mapper->from($foreignData, [
            'Response.Item.Id',
            'Response.Item.Name',
        ])->to($item, [
            'setId',
            'setName',
        ])->get();

        $this->assertEquals('iPhone 8', $item->getName());
    }

    /**
     * @test
     */
    public function it_can_set_value_using_callback()
    {
        $foreignData = [
            'Response' => [
                'Item' => [
                    'Id'   => 1234,
                    'Name' => 'iPhone 8',
                ],
            ],
        ];

        $item = new Item();

        $mapper = new Mapper([]);

        $item = $mapper->from($foreignData, [
            'Response.Item.Id',
            function ($data) {
                return strtoupper($data['Response']['Item']['Name']);
            },
        ])->to($item, [
            'setId',
            'setName',
        ])->get();

        $this->assertEquals('IPHONE 8', $item->getName());
    }

    /**
     * @test
     */
    public function it_can_use_a_mappable()
    {
        require 'ProductMap.php';

        $response             = new \stdClass();
        $response->Item       = new \stdClass();
        $response->Item->Id   = 1234;
        $response->Item->Name = "iPhone 8";

        $product = new \stdClass();

        $product = ProductMap::map($response, $product);

        $this->assertEquals(1234, $product->id);
        $this->assertEquals('iPhone 8', $product->name);
        $this->assertEquals('This is the title for: iPhone 8', $product->title);
        $this->assertEquals(1000.99, $product->price); // From Defaults
        $this->assertEquals("10%", $product->discount); // From Defaults
    }

    /**
     * @test
     */
    public function it_can_call_a_source_method()
    {
        $item    = new SourceItem();
        $product = new \stdClass();

        $mapper = new Mapper();

        $item = $mapper->from($item, ['getListId'])->to($product, ['listId'])->get();


        $this->assertEquals('list-123', $product->listId);
    }

}
