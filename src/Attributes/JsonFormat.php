<?php

namespace HoangDo\JsonApi\Attributes;

use Attribute;
use Illuminate\Support\Facades\App;
use HoangDo\JsonApi\Format\Formatter;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class JsonFormat
{
    public array $formatArgs;
    public Formatter $formatter;

    public function __construct(
        public string $formatterClass,
        ...$formatArgs
    )
    {
        $this->formatter = App::make($this->formatterClass);
        $this->formatArgs = $formatArgs;
    }
}
