<?php
namespace Redoy\CoreModule\Traits;


use Illuminate\Http\JsonResponse;
use Redoy\CoreModule\Constants\ApiCodes;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

trait ResponseHelperTrait
{
    public function successResponse($data = null, ?int $api_code = null, ?string $message = null): JsonResponse
    {
        return ResponseBuilder::asSuccess($api_code ?? ApiCodes::OK)
            ->withData($data)
            ->withMessage($message)
            ->build();
    }

    public function errorResponse($data = null, ?int $api_code = null, ?string $message = null): JsonResponse
    {
        return ResponseBuilder::asError($api_code ?? ApiCodes::BAD_REQUEST)
            ->withData($data)
            ->withMessage($message)
            ->build();
    }
}
