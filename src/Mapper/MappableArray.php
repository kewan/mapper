<?php


namespace Kewan\Mapper;


class MappableArray extends Mappable
{
    private $mappings = [];

    public function __construct($mappings)
    {
        $this->mappings = $mappings;
    }

    public function getMap(): array
    {
        return $this->mappings;
    }

}