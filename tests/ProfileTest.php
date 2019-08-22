<?php

namespace TenantCloud\Cors\Tests;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use TenantCloud\Cors\CorsMiddleware;
use TenantCloud\Cors\Tests\Stubs\TestCorsProfileStub;

class ProfileTest extends TestCase
{
	public function testItWillThrowAnExceptionWhenANonExistentProfileIsGiven(): void
	{
		Route::post('test', function () {})->middleware(CorsMiddleware::class . ':nesto');

		$this
			->sendRequest('POST', 'https://tenantcloud.com')
			->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
	}

	public function testItWillUseConfigValuesIfProfileFromConfigIsGiven(): void
	{
		config(['cors.profiles.nesto' => []]);

		Route::post('test', function () {})->middleware(CorsMiddleware::class . ':nesto');

		$this
			->sendRequest('POST', 'https://tenantcloud.com')
			->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function testItWillUseConfigValuesIfProfileClassIsGiven(): void
	{
		Route::post('test', function () {})->middleware(CorsMiddleware::class . ':' . TestCorsProfileStub::class);

		$this
			->sendRequest('POST', 'https://tenantcloud.com')
			->assertStatus(Response::HTTP_FORBIDDEN);

		$this
			->sendRequest('POST', 'https://nesto.com')
			->assertStatus(Response::HTTP_OK);
	}

	public function sendRequest(string $method, string $origin, string $uri = 'test'): TestResponse
	{
		$headers = [
			'Origin' => $origin,
		];

		$server = $this->transformHeadersToServerVars($headers);

		return $this->call($method, $uri, [], [], [], $server);
	}
}