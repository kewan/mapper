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

        $this->defaults = $mappable->getDefaults();
        $this->mappings = array_merge($this->defaults, $mappable->getMap());

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
            } else if ($this->canBeCalled($this->source, $sourcePath)) {
                $value = $this->source->{$sourcePath}();
            } else {
                $value = data_get($this->source, $sourcePath);
            }

            if (!$value && Arr::has($this->defaults, $destinationPath)) {
                $value = Arr::get($this->defaults, $destinationPath);
            }

            if ($this->canBeCalled($this->destination, $destinationPath)) {
                $this->destination->{$destinationPath}($value);
            } else {
                data_set($this->destination, $destinationPath, $value);
            }
        }

        return $this->destination;
    }

    private function canBeCalled($object, $method)
    {
        return !preg_match('/\./', $method) && is_callable([$object, $method]);
    }


}