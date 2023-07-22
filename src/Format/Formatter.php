<?php

namespace HoangDo\JsonApi\Format;

interface Formatter
{
    public function format($object, ...$args);
}
