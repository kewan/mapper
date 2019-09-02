<?php


namespace Kewan\Mapper;


abstract class Mappable
{
    abstract public function getMap(): array;

    public function getDefaults(): array
    {
        return [];
    }

    public final static function map($source, $destination)
    {
        return (new Mapper(new static()))->from($source)->to($destination)->get();
    }
}