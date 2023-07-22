<?php

namespace HoangDo\JsonApi\Middlewares;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use IteratorAggregate;
use ReflectionException;
use HoangDo\JsonApi\Attributes\JsonFormat;
use HoangDo\JsonApi\Attributes\JsonMessage;
use HoangDo\JsonApi\Format\Formatter;
use HoangDo\JsonApi\Format\JsonApiResponseFactory;
use HoangDo\JsonApi\Helpers\Utils;
use \Illuminate\Http\Client\Response as ClientResponse;

class JsonResponseMiddleware
{
    public function __construct(
        protected JsonApiResponseFactory $factory
    )
    {
    }

    private function formatArrayable($data, Formatter $formatter, $formatArgs): array
    {
        $formatted = [];
        foreach ($data as $item) {
            $formatted[] = $formatter->format($item, ...$formatArgs);
        }
        return $formatted;
    }

    /**
     * @throws ReflectionException
     */
    private function formatControllerData($controllerData)
    {
        $attributes = Utils::currentRouteActionAttrs(JsonFormat::class);
        $data = $controllerData;

        foreach ($attributes as $attribute) {
            /** @var JsonFormat $jsonFormat */
            $jsonFormat = $attribute->newInstance();
            /** @var Formatter $formatter */
            $formatter = $jsonFormat->formatter;
            $formatArgs = $jsonFormat->formatArgs;

            if ($data instanceof LengthAwarePaginator) {
                return $this->formatArrayable($data->items(), $formatter, $formatArgs);
                //return $data->setCollection($collection);
            }

            if ($data instanceof IteratorAggregate) {
                return $this->formatArrayable($data, $formatter, $formatArgs);
            }

            return $formatter->format($data, ...$formatArgs);
        }

        if ($data instanceof LengthAwarePaginator) {
            return $data->items();
        }

        return $data;
    }

    /**
     * @throws ReflectionException
     */
    private function getJsonMessage()
    {
        $attributes = Utils::currentRouteActionAttrs(JsonMessage::class);
        $translator = App::make('translator');

        foreach ($attributes as $attribute) {
            /** @var JsonMessage $instance */
            $instance = $attribute->newInstance();
            return $instance->message
                ? $translator->get($instance->message)
                : $instance->message;
        }

        return $translator->get('messages.success');
    }

    /**
     * @param ClientResponse $httpResponse
     * @return JsonResponse|Response
     */
    private function buildProxyResponse(ClientResponse $httpResponse)
    {
        $statusCode = $httpResponse->status();
        //$header = $httpResponse->headers();
        $json = $httpResponse->json();
        $response = $json
            ? new JsonResponse($json)
            : new Response($httpResponse->body());

        return $response->setStatusCode($statusCode);
    }

    /**
     * @throws ReflectionException
     */
    public function handle($req, Closure $next)
    {
        /** @var Response $controllerResponse */
        $controllerResponse = $next($req);
        if (!in_array($controllerResponse->getStatusCode(), [200, 201]))
            return $controllerResponse;
        $originalData = $controllerResponse->original;

        if ($originalData instanceof ClientResponse) {
            return $this->buildProxyResponse($originalData);
        }

        if (!isset($originalData)) {
            return $this->factory->format($originalData, null, $this->getJsonMessage(), $originalData);
        }

        if ($controllerResponse instanceof JsonResponse) {
            $originData = $controllerResponse->original;

            $data = $this->formatControllerData($originData);
            if ($originData instanceof LengthAwarePaginator) {
                $arrayData = array_map(
                    fn($e) => $e instanceof Arrayable ? $e->toArray() : $e,
                    $data
                );
                return $this->factory->format(
                    $arrayData,
                    Utils::metaFromPaginator($originData),
                    $this->getJsonMessage(),
                    $originalData,
                );
            } else {
                return $this->factory->format(
                    $data,
                    null,
                    $this->getJsonMessage(),
                    $originalData,
                );
            }
        }
        return $controllerResponse;
    }
}