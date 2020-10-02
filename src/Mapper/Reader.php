<?php


namespace Kewan\Mapper;


class Reader
{
    private $subject;

    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    public function get($path, $default=null)
    {
        if (is_callable($path)) {
            $value = $path($this->subject);
        } else if ($this->canBeCalled($this->subject, $path)) {
            $value = $this->subject->{$path}();
        } else {
            $value = data_get($this->subject, $path);
        }

        return $value ?: $default;
    }

    private function canBeCalled($object, $method)
    {
        return !preg_match('/\./', $method) && is_callable([$object, $method]);
    }
}