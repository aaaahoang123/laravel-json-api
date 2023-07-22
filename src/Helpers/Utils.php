<?php

namespace HoangDo\JsonApi\Helpers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;

class Utils
{
    /**
     * @param string $attributeClassName
     * @return array|ReflectionAttribute[]
     * @throws ReflectionException
     */
    public static function currentRouteActionAttrs(string $attributeClassName): array
    {
        $action = Route::currentRouteAction();
        if (!$action) {
            return [];
        }

        list($className, $methodName) = explode('@', $action);

        $reflection = new ReflectionClass($className);
        $classAttributes = $reflection->getAttributes($attributeClassName);

        $method = $reflection->getMethod($methodName);
        $methodAttributes = $method->getAttributes($attributeClassName);

        return array_merge($methodAttributes, $classAttributes);
    }

    public static function metaFromPaginator(LengthAwarePaginator $paginator): array
    {
        $paginator->appends(Request::query());

        return [
            'total' => $paginator->total(),
            'limit' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'next_url' => $paginator->nextPageUrl(),
            'prev_url' => $paginator->previousPageUrl()
        ];
    }
}