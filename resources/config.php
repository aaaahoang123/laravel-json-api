<?php

return [
    'middleware_name' => explode(',', env('JSON_MIDDLEWARE_NAME', 'json')),
    'response_factory' => \HoangDo\JsonApi\Format\BasicJsonApiResponseFactory::class,
];