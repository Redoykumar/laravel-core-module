<?php

namespace Redoy\CoreModule\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Redoy\CoreModule\Traits\ResponseHelperTrait;
use Illuminate\Http\JsonResponse;

/**
 * Loads test cases from test_cases.json and runs a broad set of combinations
 * including cases that are expected to throw exceptions (e.g. wrong api_code type)
 */
class JsonDrivenResponseHelperTest extends TestCase
{
    private array $cases = [];

    protected function setUp(): void
    {
        // make sure autoload is present (bootstrap loads vendor autoload)
        require_once __DIR__ . '/../bootstrap.php';

        // load a local test double for ResponseBuilder so we don't need a full app container
        require_once __DIR__ . '/../Support/TestResponseBuilder.php';

        // Use the cleaned JSON fixture to avoid parsing issues in the older file
        $jsonFile = __DIR__ . '/../test_cases.json';
        $raw = file_get_contents($jsonFile);
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            $this->fail('Could not decode test_cases.json or it is not an array');
        }
        $this->cases = $decoded;
    }

    public function testJsonDrivenCases(): void
    {
        foreach ($this->cases as $case) {
            $id = $case['id'] ?? '(no-id)';
            $name = $case['name'] ?? 'unnamed';
            $this->assertArrayHasKey('method', $case, "Case $id must declare method");

            $method = $case['method'];
            $data = array_key_exists('data', $case) ? $case['data'] : null;
            $api_code = array_key_exists('api_code', $case) ? $case['api_code'] : null;
            $message = array_key_exists('message', $case) ? $case['message'] : null;
            $expectException = $case['expect_exception'] ?? null;

            $obj = new class {
                use ResponseHelperTrait;
            };

            // Run each case as a subtest for better isolation
            $this->runCase($id, $name, $obj, $method, $data, $api_code, $message, $expectException);
        }
    }

    private function runCase(string $id, string $name, object $obj, string $method, $data, $api_code, $message, $expectException): void
    {
        $label = "[$id] $name";

        try {
            $result = $obj->{$method}($data, $api_code, $message);

            if ($expectException) {
                $this->fail("$label expected exception $expectException but call succeeded");
                return;
            }

            // Basic assertions about the response
            $this->assertInstanceOf(JsonResponse::class, $result, "$label must return JsonResponse");

            $content = $result->getContent();
            $decoded = json_decode($content, true);
            $this->assertNotFalse($decoded, "$label response content must be valid JSON");

            // If data was provided, verify it appears (best-effort, when possible)
            if ($data !== null) {
                // ResponseBuilder places data under 'data' key commonly; check for it
                if (is_array($decoded) && array_key_exists('data', $decoded)) {
                    $this->assertNotNull($decoded['data'], "$label response must contain data key");
                } else {
                    // otherwise, at least ensure JSON encoded form contains something
                    $this->assertNotEmpty($content, "$label response body should not be empty");
                }
            }
        } catch (\Throwable $e) {
            if ($expectException) {
                $this->assertInstanceOf($expectException, $e, "$label expected exception type did not match");
                return;
            }

            // Unexpected exception
            $this->fail("$label threw unexpected exception: " . get_class($e) . ' - ' . $e->getMessage());
        }
    }
}
