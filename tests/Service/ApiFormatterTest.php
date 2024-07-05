<?php

namespace App\Tests\Service;

use App\Services\ApiFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiFormatterTest extends TestCase
{
    private ApiFormatter $apiFormatter;

    protected function setUp(): void
    {
        $this->apiFormatter = new ApiFormatter();
    }

    public function testFormatResponse()
    {
        $data = ['key' => 'value'];
        $statusCode = Response::HTTP_OK;

        $response = $this->apiFormatter->formatResponse($data, $statusCode);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['status' => $statusCode, 'data' => $data]),
            $response->getContent()
        );
    }

    public function testFormatResponseWithCustomStatusCode()
    {
        $data = ['key' => 'value'];
        $statusCode = Response::HTTP_CREATED;

        $response = $this->apiFormatter->formatResponse($data, $statusCode);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['status' => $statusCode, 'data' => $data]),
            $response->getContent()
        );
    }

    public function testFormatError()
    {
        $message = 'An error occurred';
        $statusCode = Response::HTTP_BAD_REQUEST;

        $response = $this->apiFormatter->formatError($message, $statusCode);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['status' => $statusCode, 'error' => $message]),
            $response->getContent()
        );
    }

    public function testFormatErrorWithCustomStatusCode()
    {
        $message = 'An error occurred';
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        $response = $this->apiFormatter->formatError($message, $statusCode);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['status' => $statusCode, 'error' => $message]),
            $response->getContent()
        );
    }
}

