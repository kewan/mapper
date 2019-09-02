<?php

namespace Kewan\Mapper;

use Illuminate\Support\Arr;

class Mapper
{
    private $source;

    private $destination;
    private $defaults;
    private $mappings;

    /** @var Mappable $mappable */
    private $mappable;

    public function __construct($mappable=[])
    {
        $this->using($mappable);
    }

    public function from($source)
    {
        $this->source = $source;

        return $this;
    }

    public function to($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    public function using($mappable)
    {
        if(!$mappable instanceof Mappable) {
            $mappable = new MappableArray($mappable);
        }

        $this->mappings = $mappable->getMap();
        $this->defaults = $mappable->getDefaults();

        return $this;
    }

    public function defaults($defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    public function get()
    {
        foreach ($this->mappings as $destinationPath => $sourcePath) {

            if (is_callable($sourcePath)) {
                $value = $sourcePath($this->source);
            } else {
                $value = data_get($this->source, $sourcePath);
            }

            if (!$value && Arr::has($this->defaults, $destinationPath)) {
                $value = Arr::get($this->defaults, $destinationPath);
            }

            if (method_exists($this->destination, $destinationPath)) {
                $this->destination->{$destinationPath}($value);
            } else {
                data_set($this->destination, $destinationPath, $value);
            }
        }

        return $this->destination;
    }
}