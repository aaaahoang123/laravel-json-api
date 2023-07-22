<?php

namespace HoangDo\JsonApi\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class JsonMessage
{
    public function __construct(
        public string $message
    )
    {
    }
}
