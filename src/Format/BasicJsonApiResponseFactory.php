<?php

namespace HoangDo\JsonApi\Format;

use Illuminate\Http\JsonResponse;

class BasicJsonApiResponseFactory implements JsonApiResponseFactory
{
    protected ?bool $printCallStack = null;
    public function format($formattedData, $meta, $message, $rawData): JsonResponse
    {
        $resp = [
            'status' => 'success',
            'message' => $message,
            'data' => $formattedData,
            'times' => now()->toISOString(),
        ];

        $printCallStack = $this->printCallStack ?? config('app.debug');
        if ($printCallStack) {
            $resp['call_stack'] = debug_backtrace();
        }

        if (isset($meta)) {
            $resp['meta'] = $meta;
        }

        return new JsonResponse($resp);
    }
}