<?php

namespace Tests\Unit;

use App\Http\Controllers\TuiterApiController;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class TuiterApiControllerTest extends TestCase
{
    /**
     * @var TuiterApiController
     */
    protected TuiterApiController $controller;

    /**
     * @var string Base API URL for test requests
     */
    protected string $tuiterHost = 'https://test-api.tuiter.com';

    /**
     * @var string Proxy host URL for test environment
     */
    protected string $proxyHost = 'https://test-proxy.tuiter.com';

    /**
     * @var string Secret API key for authentication
     */
    protected string $apiSecret = 'test-api-secret';

    /**
     * Set up the test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        Config::set('custom.proxy_host', $this->proxyHost);
        Config::set('custom.tuiter_host', $this->tuiterHost);
        Config::set('custom.tuiter_api_key', $this->apiSecret);

        $this->controller = new TuiterApiController();
    }

    /**
     * Clean up after test
     */
    protected function tearDown(): void
    {
        // Reset HTTP fakes
        Http::swap(app(Factory::class));

        parent::tearDown();
    }

    /**
     * Test the login endpoint
     */
    public function testLogin(): void
    {
        // Given - Test data and mocks
        $requestData = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];
        $mockResponseData = [
            'name' => '2b',
            'email' => 'test@test.com',
            'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6InRlc3RAdGVzdC5jb20iLCJleHAiOjE3NTE5OTUwMjQsImlzcyI6InVubGFtLXR1aXRlciIsIm5hbWUiOiJ0ZXN0QHRlc3QuY29tIiwic3ViIjoyfQ.cscbmwj4ZUmTBFPwRzNcGDrBvuYLG9Mtv9J9pzYFB8g'
        ];

        // Create request
        $request = Request::create('/api/v1/login', 'POST', $requestData);

        // Capture all HTTP requests for debugging
        $capturedRequests = [];

        Http::fake([
            '*' => function ($request) use ($mockResponseData, &$capturedRequests) {
                // Store request details for debugging
                $capturedRequests[] = [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'headers' => $request->headers(),
                    'body' => $request->body()
                ];

                return Http::response($mockResponseData, 200, ['Content-Type' => 'application/json']);
            }
        ]);

        // When - Execute the controller method
        $response = $this->controller->login($request);

        // Basic verification
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals(json_encode($mockResponseData), $response->getContent());

        // Verify at least one request was sent
        $this->assertNotEmpty($capturedRequests, 'No HTTP requests were captured');

        // Verify first request details
        $this->assertEquals('POST', $capturedRequests[0]['method']);
        $this->assertStringContainsString($this->tuiterHost, $capturedRequests[0]['url']);
        $this->assertStringContainsString('/v1/login', $capturedRequests[0]['url']);
    }

    /**
     * Test the createUser endpoint
     */
    public function testCreateUser(): void
    {
        // Given - Test data and mocks
        $requestData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123'
        ];
        $mockResponseData = [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ];

        // Create request
        $request = Request::create('/api/v1/users', 'POST', $requestData);

        // Capture all HTTP requests for debugging
        $capturedRequests = [];

        Http::fake([
            '*' => function ($request) use ($mockResponseData, &$capturedRequests) {
                // Store request details for debugging
                $capturedRequests[] = [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'headers' => $request->headers(),
                    'body' => $request->body()
                ];

                return Http::response($mockResponseData, 200, ['Content-Type' => 'application/json']);
            }
        ]);

        // When - Execute the controller method
        $response = $this->controller->createUser($request);

        // Then - Verify captured requests through assertions
        // Basic verification
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals(json_encode($mockResponseData), $response->getContent());

        // Verify at least one request was sent
        $this->assertNotEmpty($capturedRequests, 'No HTTP requests were captured');

        // Verify first request details
        $this->assertEquals('POST', $capturedRequests[0]['method']);
        $this->assertStringContainsString($this->tuiterHost, $capturedRequests[0]['url']);
        $this->assertStringContainsString('/v1/users', $capturedRequests[0]['url']);
    }

    /**
     * Test the feed endpoint
     */
    public function testFeed(): void
    {
        // Given - Test data and mocks
        $queryParams = ['page' => 1, 'only_parents' => true];
        $userToken = 'user-auth-token';
        $mockResponseData = ['tuits' => [['id' => 1, 'message' => 'Test tuit']]];

        // Create request
        $request = Request::create('/api/v1/me/feed', 'GET', $queryParams);
        $request->headers->set('Authorization', $userToken);

        // Capture all HTTP requests for debugging
        $capturedRequests = [];

        Http::fake([
            '*' => function ($request) use ($mockResponseData, &$capturedRequests) {
                // Store request details for debugging
                $capturedRequests[] = [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'headers' => $request->headers(),
                ];

                return Http::response($mockResponseData, 200, ['Content-Type' => 'application/json']);
            }
        ]);

        // When - Execute the controller method
        $response = $this->controller->feed($request);

        // Then - Log the captured requests for debugging
        Log::info('Captured HTTP requests in testFeed:', $capturedRequests);

        // Basic verification
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals(json_encode($mockResponseData), $response->getContent());

        // Verify at least one request was sent
        $this->assertNotEmpty($capturedRequests, 'No HTTP requests were captured');

        // Verify first request details
        $this->assertEquals('GET', $capturedRequests[0]['method']);
        $this->assertStringContainsString($this->tuiterHost, $capturedRequests[0]['url']);
        $this->assertStringContainsString('/v1/me/feed', $capturedRequests[0]['url']);
        $this->assertArrayHasKey('Authorization', $capturedRequests[0]['headers']);
        $this->assertEquals($userToken, $capturedRequests[0]['headers']['Authorization'][0]);
    }

    /**
     * Test the profile endpoint
     */
    public function testProfile(): void
    {
        // Given - Test data and mocks
        $userToken = 'user-auth-token';
        $mockResponseData = [
            'id' => 123,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'avatar_url' => 'https://example.com/avatar.jpg',
        ];

        // Create request
        $request = Mockery::mock('Illuminate\Http\Request');
        $request->shouldReceive('header')->once()->with('Authorization')->andReturn($userToken);

        // Capture all HTTP requests for debugging
        $capturedRequests = [];

        Http::fake([
            '*' => function ($request) use ($mockResponseData, &$capturedRequests) {
                // Store request details for debugging
                $capturedRequests[] = [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'headers' => $request->headers()
                ];

                return Http::response($mockResponseData, 200, ['Content-Type' => 'application/json']);
            }
        ]);

        // When - Execute the controller method
        $response = $this->controller->profile($request);

        // Then - Log the captured requests for debugging
        Log::info('Captured HTTP requests in testProfile:', $capturedRequests);

        // Basic verification
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals(json_encode($mockResponseData), $response->getContent());

        // Verify at least one request was sent
        $this->assertNotEmpty($capturedRequests, 'No HTTP requests were captured');

        // Verify first request details
        $this->assertEquals('GET', $capturedRequests[0]['method']);
        $this->assertStringContainsString($this->tuiterHost, $capturedRequests[0]['url']);
        $this->assertStringContainsString('/v1/me/profile', $capturedRequests[0]['url']);
        $this->assertArrayHasKey('Authorization', $capturedRequests[0]['headers']);
    }

    /**
     * Test the updateProfile endpoint
     */
    public function testUpdateProfile(): void
    {
        // Given - Test data and mocks
        $requestData = [
            'name' => 'Updated User',
            'avatar_url' => 'https://example.com/avatar.jpg',
            'password' => 'newpassword123'
        ];
        $userToken = 'user-auth-token';
        $mockResponseData = [
            'id' => 123,
            'name' => 'Updated User',
            'email' => 'test@example.com',
            'avatar_url' => 'https://example.com/avatar.jpg',
        ];

        // Create request
        $request = Request::create('/api/v1/me/profile', 'PUT', $requestData);
        $request->headers->set('Authorization', $userToken);

        // Capture all HTTP requests for debugging
        $capturedRequests = [];

        Http::fake([
            '*' => function ($request) use ($mockResponseData, &$capturedRequests) {
                // Store request details for debugging
                $capturedRequests[] = [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'headers' => $request->headers(),
                    'body' => $request->body()
                ];

                return Http::response($mockResponseData, 200, ['Content-Type' => 'application/json']);
            }
        ]);

        // When - Execute the controller method
        $response = $this->controller->updateProfile($request);

        // Then - Log the captured requests for debugging
        Log::info('Captured HTTP requests in testUpdateProfile:', $capturedRequests);

        // Basic verification
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals(json_encode($mockResponseData), $response->getContent());

        // Verify at least one request was sent
        $this->assertNotEmpty($capturedRequests, 'No HTTP requests were captured');

        // Verify first request details
        $this->assertEquals('PUT', $capturedRequests[0]['method']);
        $this->assertStringContainsString($this->tuiterHost, $capturedRequests[0]['url']);
        $this->assertStringContainsString('/v1/me/profile', $capturedRequests[0]['url']);
    }

    /**
     * Test the createTuit endpoint
     */
    public function testCreateTuit(): void
    {
        // Given - Test data and mocks
        $requestData = ['message' => 'Test tuit message'];
        $userToken = 'user-auth-token';
        $mockResponseData = [
            'id' => 123,
            'message' => 'Test tuit message',
            'created_at' => '2025-06-08T12:00:00Z'
        ];

        // Create request
        $request = Request::create('/api/v1/me/tuits', 'POST', $requestData);
        $request->headers->set('Authorization', $userToken);

        // Capture all HTTP requests for debugging
        $capturedRequests = [];

        Http::fake([
            '*' => function ($request) use ($mockResponseData, &$capturedRequests) {
                // Store request details for debugging
                $capturedRequests[] = [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'headers' => $request->headers(),
                    'body' => $request->body()
                ];

                return Http::response($mockResponseData, 200, ['Content-Type' => 'application/json']);
            }
        ]);

        // When - Execute the controller method
        $response = $this->controller->createTuit($request);

        // Then - Log the captured requests for debugging
        Log::info('Captured HTTP requests in testCreateTuit:', $capturedRequests);

        // Basic verification
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals(json_encode($mockResponseData), $response->getContent());

        // Verify at least one request was sent
        $this->assertNotEmpty($capturedRequests, 'No HTTP requests were captured');

        // Verify first request details
        $this->assertEquals('POST', $capturedRequests[0]['method']);
        $this->assertStringContainsString($this->tuiterHost, $capturedRequests[0]['url']);
        $this->assertStringContainsString('/v1/me/tuits', $capturedRequests[0]['url']);
    }

    /**
     * Test the showTuit endpoint
     */
    public function testShowTuit(): void
    {
        // Given - Test data and mocks
        $tuitId = 123;
        $userToken = 'user-auth-token';
        $mockResponseData = [
            'id' => $tuitId,
            'message' => 'Test tuit content',
            'created_at' => '2025-06-08T10:00:00Z',
            'user' => [
                'id' => 456,
                'name' => 'Test User'
            ]
        ];

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('header')->once()->with('Authorization')->andReturn($userToken);
        $request->shouldReceive('route')->once()->with('tuit_id')->andReturn($tuitId);


        // Capture all HTTP requests for debugging
        $capturedRequests = [];

        Http::fake([
            '*' => function ($request) use ($mockResponseData, &$capturedRequests) {
                // Store request details for debugging
                $capturedRequests[] = [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'headers' => $request->headers()
                ];

                return Http::response($mockResponseData, 200, ['Content-Type' => 'application/json']);
            }
        ]);

        // When - Execute the controller method
        $response = $this->controller->showTuit($request);

        // Basic verification
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals(json_encode($mockResponseData), $response->getContent());

        // Verify at least one request was sent
        $this->assertNotEmpty($capturedRequests, 'No HTTP requests were captured');

        // Verify first request details
        $this->assertEquals('GET', $capturedRequests[0]['method']);
        $this->assertStringContainsString($this->tuiterHost, $capturedRequests[0]['url']);
        $this->assertStringContainsString('/v1/me/tuits/' . $tuitId, $capturedRequests[0]['url']);
    }

    /**
     * Test the addLike endpoint
     */
    public function testAddLike(): void
    {
        // Given - Test data and mocks
        $tuitId = 123;
        $userToken = 'user-auth-token';
        $mockResponseData = [
            'success' => true,
            'message' => 'Like added successfully'
        ];

        // Create request with Mockery
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('header')->once()->with('Authorization')->andReturn($userToken);
        $request->shouldReceive('route')->once()->with('tuit_id')->andReturn($tuitId);

        // Capture all HTTP requests for debugging
        $capturedRequests = [];

        Http::fake([
            '*' => function ($request) use ($mockResponseData, &$capturedRequests) {
                // Store request details for debugging
                $capturedRequests[] = [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'headers' => $request->headers()
                ];

                return Http::response($mockResponseData, 200, ['Content-Type' => 'application/json']);
            }
        ]);

        // When - Execute the controller method
        $response = $this->controller->addLike($request);

        // Then - Log the captured requests for debugging
        Log::info('Captured HTTP requests in testAddLike:', $capturedRequests);

        // Basic verification
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals(json_encode($mockResponseData), $response->getContent());

        // Verify at least one request was sent
        $this->assertNotEmpty($capturedRequests, 'No HTTP requests were captured');

        // Verify first request details
        $this->assertEquals('POST', $capturedRequests[0]['method']);
        $this->assertStringContainsString($this->tuiterHost, $capturedRequests[0]['url']);
        $this->assertStringContainsString('/v1/me/tuits/' . $tuitId . '/likes', $capturedRequests[0]['url']);
    }

    /**
     * Test the removeLike endpoint
     */
    public function testRemoveLike(): void
    {
        // Given - Test data and mocks
        $tuitId = 123;
        $userToken = 'user-auth-token';
        $mockResponseData = [
            'success' => true,
            'message' => 'Like removed successfully'
        ];

        // Create request with Mockery
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('header')->once()->with('Authorization')->andReturn($userToken);
        $request->shouldReceive('route')->once()->with('tuit_id')->andReturn($tuitId);

        // Capture all HTTP requests for debugging
        $capturedRequests = [];

        Http::fake([
            '*' => function ($request) use ($mockResponseData, &$capturedRequests) {
                // Store request details for debugging
                $capturedRequests[] = [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'headers' => $request->headers()
                ];

                return Http::response($mockResponseData, 200, ['Content-Type' => 'application/json']);
            }
        ]);

        // When - Execute the controller method
        $response = $this->controller->removeLike($request);

        // Then - Log the captured requests for debugging
        Log::info('Captured HTTP requests in testRemoveLike:', $capturedRequests);

        // Basic verification
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals(json_encode($mockResponseData), $response->getContent());

        // Verify at least one request was sent
        $this->assertNotEmpty($capturedRequests, 'No HTTP requests were captured');

        // Verify first request details
        $this->assertEquals('DELETE', $capturedRequests[0]['method']);
        $this->assertStringContainsString($this->tuiterHost, $capturedRequests[0]['url']);
        $this->assertStringContainsString('/v1/me/tuits/' . $tuitId . '/likes', $capturedRequests[0]['url']);
    }

    /**
     * Test the tuitReplies endpoint
     */
    public function testTuitReplies(): void
    {
        // Given - Test data and mocks
        $tuitId = 123;
        $userToken = 'user-auth-token';
        $mockResponseData = [
            'replies' => [
                [
                    'id' => 456,
                    'message' => 'Test reply 1',
                    'created_at' => '2025-06-08T12:30:00Z'
                ],
                [
                    'id' => 457,
                    'message' => 'Test reply 2',
                    'created_at' => '2025-06-08T12:35:00Z'
                ]
            ]
        ];

        // Create request with Mockery
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('header')->once()->with('Authorization')->andReturn($userToken);
        $request->shouldReceive('route')->once()->with('tuit_id')->andReturn($tuitId);

        // Capture all HTTP requests for debugging
        $capturedRequests = [];

        Http::fake([
            '*' => function ($request) use ($mockResponseData, &$capturedRequests) {
                // Store request details for debugging
                $capturedRequests[] = [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'headers' => $request->headers()
                ];

                return Http::response($mockResponseData, 200, ['Content-Type' => 'application/json']);
            }
        ]);

        // When - Execute the controller method
        $response = $this->controller->tuitReplies($request);

        // Then - Log the captured requests for debugging
        Log::info('Captured HTTP requests in testTuitReplies:', $capturedRequests);

        // Basic verification
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals(json_encode($mockResponseData), $response->getContent());

        // Verify at least one request was sent
        $this->assertNotEmpty($capturedRequests, 'No HTTP requests were captured');

        // Verify first request details
        $this->assertEquals('GET', $capturedRequests[0]['method']);
        $this->assertStringContainsString($this->tuiterHost, $capturedRequests[0]['url']);
        $this->assertStringContainsString('/v1/me/feed/' . $tuitId . '/replies', $capturedRequests[0]['url']);
    }

    /**
     * Test the createReply endpoint
     */
    public function testCreateReply(): void
    {
        // Given - Test data and mocks
        $tuitId = 123;
        $requestData = ['message' => 'Test reply message'];
        $userToken = 'user-auth-token';
        $mockResponseData = [
            'id' => 456,
            'message' => 'Test reply message',
            'created_at' => '2025-06-08T13:00:00Z',
            'in_reply_to' => $tuitId
        ];

        // Create request with Mockery
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('header')->once()->with('Authorization')->andReturn($userToken);
        $request->shouldReceive('route')->once()->with('tuit_id')->andReturn($tuitId);
        $request->shouldReceive('all')->once()->andReturn($requestData);

        // Capture all HTTP requests for debugging
        $capturedRequests = [];

        Http::fake([
            '*' => function ($request) use ($mockResponseData, &$capturedRequests) {
                // Store request details for debugging
                $capturedRequests[] = [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'headers' => $request->headers(),
                    'body' => $request->body()
                ];

                return Http::response($mockResponseData, 200, ['Content-Type' => 'application/json']);
            }
        ]);

        // When - Execute the controller method
        $response = $this->controller->createReply($request);

        // Then - Log the captured requests for debugging
        Log::info('Captured HTTP requests in testCreateReply:', $capturedRequests);

        // Basic verification
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals(json_encode($mockResponseData), $response->getContent());

        // Verify at least one request was sent
        $this->assertNotEmpty($capturedRequests, 'No HTTP requests were captured');

        // Verify first request details
        $this->assertEquals('POST', $capturedRequests[0]['method']);
        $this->assertStringContainsString($this->tuiterHost, $capturedRequests[0]['url']);
        $this->assertStringContainsString('/v1/tuits/' . $tuitId . '/replies', $capturedRequests[0]['url']);
    }
}

