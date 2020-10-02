<?php

namespace Kewan\Mapper;

use Illuminate\Support\Arr;

class Mapper
{
    private $source;

    private $reader;

    private $destination;
    private $defaults;

    private $mappings;

    private $sourcePaths = [];
    private $destinationPaths = [];

    public function __construct($mappable = [])
    {
        if (!$mappable instanceof Mappable) {
            $mappable = new MappableArray($mappable);
        }

        $this->defaults = $mappable->getDefaults();
        $this->mappings = array_merge($this->defaults, $mappable->getMap());

        return $this;
    }

    public function from($source, $sourcePaths = [])
    {
        $this->source = $source;
        $this->reader = new Reader($source);

        if (!empty($sourcePaths)) {
            $this->sourcePaths = $sourcePaths;
        }

        return $this;
    }

    public function to($destination, $destinationPaths = [])
    {
        $this->destination = $destination;

        if (!empty($destinationPaths)) {
            $this->destinationPaths = $destinationPaths;
        }

        return $this;
    }

    public function withDefaults($defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    public function get()
    {
        foreach ($this->getMappings() as $destinationPath => $sourcePath) {

            $value = $this->reader->get($sourcePath);

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

    private function getMappings()
    {
        $sourceCount      = count($this->sourcePaths);
        $destinationCount = count($this->destinationPaths);

        if ($sourceCount > 0 || $destinationCount > 0) {
            return array_combine($this->destinationPaths, $this->sourcePaths);
        }

        return $this->mappings;
    }

    private function canBeCalled($object, $method)
    {
        return !preg_match('/\./', $method) && is_callable([$object, $method]);
    }


}