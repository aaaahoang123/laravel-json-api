<?php

namespace HoangDo\JsonApi\Format;

use Illuminate\Http\JsonResponse;

interface JsonApiResponseFactory
{
    public function format($formattedData, $meta, $message, $rawData): JsonResponse;
}