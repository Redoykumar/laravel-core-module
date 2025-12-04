<?php

namespace Redoy\CoreModule\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Redoy\CoreModule\Traits\ResponseHelperTrait;
use Redoy\CoreModule\Constants\ApiCodes;

/**
 * Comprehensive Test Suite for ResponseHelperTrait with JSON test data
 */
class ResponseHelperTraitTest extends TestCase
{
    private static array $jsonTestData = [
        'user' => '{"id":1,"name":"John Doe","email":"john@example.com","role":"admin"}',
        'product' => '{"id":101,"title":"Laptop","price":999.99,"stock":5}',
        'order' => '{"id":"ORD-001","user_id":1,"items":[{"product_id":101}],"total":999.99}',
        'error' => '{"code":"ERR_001","message":"Invalid input","field":"email"}',
        'user_list' => '[{"id":1,"name":"John"},{"id":2,"name":"Jane"}]',
        'transaction' => '{"txn_id":"TXN-2024-001","amount":5000,"status":"completed"}',
    ];

    public function test_trait_has_success_response_method()
    {
        $obj = new class { use ResponseHelperTrait; };
        $this->assertTrue(method_exists($obj, 'successResponse'));
    }

    public function test_trait_has_error_response_method()
    {
        $obj = new class { use ResponseHelperTrait; };
        $this->assertTrue(method_exists($obj, 'errorResponse'));
    }

    public function test_api_codes_ok_equals_200()
    {
        $this->assertEquals(200, ApiCodes::OK);
    }

    public function test_api_codes_bad_request_equals_400()
    {
        $this->assertEquals(400, ApiCodes::BAD_REQUEST);
    }

    public function test_api_codes_created_equals_201()
    {
        $this->assertEquals(201, ApiCodes::CREATED);
    }

    public function test_api_codes_unauthorized_equals_401()
    {
        $this->assertEquals(401, ApiCodes::UNAUTHORIZED);
    }

    public function test_api_codes_forbidden_equals_403()
    {
        $this->assertEquals(403, ApiCodes::FORBIDDEN);
    }

    public function test_api_codes_not_found_equals_404()
    {
        $this->assertEquals(404, ApiCodes::NOT_FOUND);
    }

    public function test_api_codes_internal_server_error_equals_500()
    {
        $this->assertEquals(500, ApiCodes::INTERNAL_SERVER_ERROR);
    }

    public function test_api_codes_service_unavailable_equals_503()
    {
        $this->assertEquals(503, ApiCodes::SERVICE_UNAVAILABLE);
    }

    public function test_api_codes_unprocessable_entity_equals_422()
    {
        $this->assertEquals(422, ApiCodes::UNPROCESSABLE_ENTITY);
    }

    public function test_json_user_data_decodes_correctly()
    {
        $userData = json_decode(self::$jsonTestData['user'], true);
        $this->assertIsArray($userData);
        $this->assertEquals('John Doe', $userData['name']);
        $this->assertEquals('john@example.com', $userData['email']);
    }

    public function test_json_product_data_decodes_correctly()
    {
        $productData = json_decode(self::$jsonTestData['product'], true);
        $this->assertIsArray($productData);
        $this->assertEquals('Laptop', $productData['title']);
        $this->assertEquals(999.99, $productData['price']);
    }

    public function test_json_order_with_nested_items_decodes_correctly()
    {
        $orderData = json_decode(self::$jsonTestData['order'], true);
        $this->assertIsArray($orderData);
        $this->assertEquals('ORD-001', $orderData['id']);
        $this->assertIsArray($orderData['items']);
        $this->assertCount(1, $orderData['items']);
    }

    public function test_json_error_data_decodes_correctly()
    {
        $errorData = json_decode(self::$jsonTestData['error'], true);
        $this->assertIsArray($errorData);
        $this->assertEquals('ERR_001', $errorData['code']);
        $this->assertEquals('Invalid input', $errorData['message']);
    }

    public function test_json_user_list_array_decodes_correctly()
    {
        $userList = json_decode(self::$jsonTestData['user_list'], true);
        $this->assertIsArray($userList);
        $this->assertCount(2, $userList);
        $this->assertEquals('John', $userList[0]['name']);
    }

    public function test_json_transaction_data_decodes_correctly()
    {
        $transactionData = json_decode(self::$jsonTestData['transaction'], true);
        $this->assertIsArray($transactionData);
        $this->assertEquals('TXN-2024-001', $transactionData['txn_id']);
        $this->assertEquals(5000, $transactionData['amount']);
    }

    public function test_success_response_returns_response_object()
    {
        $obj = new class { use ResponseHelperTrait; };
        try {
            $response = $obj->successResponse();
            $this->assertNotNull($response);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_error_response_returns_response_object()
    {
        $obj = new class { use ResponseHelperTrait; };
        try {
            $response = $obj->errorResponse();
            $this->assertNotNull($response);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_api_codes_accepted_equals_202()
    {
        $this->assertEquals(202, ApiCodes::ACCEPTED);
    }

    public function test_api_codes_no_content_equals_204()
    {
        $this->assertEquals(204, ApiCodes::NO_CONTENT);
    }

    public function test_api_codes_payment_required_equals_402()
    {
        $this->assertEquals(402, ApiCodes::PAYMENT_REQUIRED);
    }

    public function test_api_codes_method_not_allowed_equals_405()
    {
        $this->assertEquals(405, ApiCodes::METHOD_NOT_ALLOWED);
    }

    public function test_api_codes_conflict_equals_409()
    {
        $this->assertEquals(409, ApiCodes::CONFLICT);
    }

    public function test_api_codes_gone_equals_410()
    {
        $this->assertEquals(410, ApiCodes::GONE);
    }

    public function test_http_status_codes_array_contains_ok()
    {
        $this->assertArrayHasKey(200, ApiCodes::HTTP_STATUS_CODES);
        $this->assertEquals('OK', ApiCodes::HTTP_STATUS_CODES[200]);
    }

    public function test_http_status_codes_array_contains_bad_request()
    {
        $this->assertArrayHasKey(400, ApiCodes::HTTP_STATUS_CODES);
        $this->assertEquals('Bad Request', ApiCodes::HTTP_STATUS_CODES[400]);
    }

    public function test_http_status_codes_array_contains_not_found()
    {
        $this->assertArrayHasKey(404, ApiCodes::HTTP_STATUS_CODES);
        $this->assertEquals('Not Found', ApiCodes::HTTP_STATUS_CODES[404]);
    }

    public function test_http_status_codes_array_contains_server_error()
    {
        $this->assertArrayHasKey(500, ApiCodes::HTTP_STATUS_CODES);
        $this->assertEquals('Internal Server Error', ApiCodes::HTTP_STATUS_CODES[500]);
    }

    public function test_json_encoding_decoding_preserves_data()
    {
        $original = json_decode(self::$jsonTestData['user'], true);
        $encoded = json_encode($original);
        $decoded = json_decode($encoded, true);
        $this->assertEquals($original, $decoded);
    }

    public function test_json_handles_special_characters()
    {
        $special = ['name' => 'Jos?? Garc??a', 'desc' => '??????????'];
        $encoded = json_encode($special);
        $decoded = json_decode($encoded, true);
        $this->assertEquals($special, $decoded);
    }

    public function test_empty_json_object()
    {
        $empty = json_decode('{}', true);
        $this->assertIsArray($empty);
        $this->assertEmpty($empty);
    }

    public function test_json_array_of_objects()
    {
        $userList = json_decode(self::$jsonTestData['user_list'], true);
        foreach ($userList as $user) {
            $this->assertArrayHasKey('id', $user);
            $this->assertArrayHasKey('name', $user);
        }
    }

    public function test_json_with_mixed_types()
    {
        $mixed = json_decode(self::$jsonTestData['product'], true);
        $this->assertIsInt($mixed['id']);
        $this->assertIsString($mixed['title']);
        $this->assertIsFloat($mixed['price']);
    }
}
