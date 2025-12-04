<?php

namespace MarcinOrlowski\ResponseBuilder;

use Illuminate\Http\JsonResponse;

/**
 * Minimal stand-in for MarcinOrlowski\ResponseBuilder\ResponseBuilder used in tests.
 * This avoids needing a full Laravel application or facade root.
 */
class ResponseBuilder
{
    private int $code;
    private $data = null;
    private $message = null;

    public static function asSuccess(?int $code = null): self
    {
        $instance = new self();
        $instance->code = $code ?? 200;
        return $instance;
    }

    public static function asError(?int $code = null): self
    {
        $instance = new self();
        $instance->code = $code ?? 400;
        return $instance;
    }

    public function withData($data): self
    {
        $this->data = $data;
        return $this;
    }

    public function withMessage(?string $msg): self
    {
        $this->message = $msg;
        return $this;
    }

    public function build(): JsonResponse
    {
        $payload = [
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data,
        ];

        return new JsonResponse($payload, $this->code);
    }
}
