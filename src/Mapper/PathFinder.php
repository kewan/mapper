<?php


namespace Kewan\Mapper;


class PathFinder
{
    private $subject;

    public function __construct($subject)
    {
        $this->subject = json_decode( json_encode($subject), true);
    }

    public function paths(): array
    {
        $keys = $this->arrayKeysMulti($this->subject);

        return $keys;
    }

    private function arrayKeysMulti(array $array)
    {
        $keys = [];

        foreach($array as $key => $value) {
            $keys[] = $key;

            if (is_array($value)) {
                $children = array_map(function($item) use ($key) {
                    return $key . '.' . $item;
                }, $this->arrayKeysMulti($value));

                $keys = array_merge($keys, $children);
            }
        }

        return $keys;

    }
}