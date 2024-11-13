<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenApi\Attributes as OA;

define("API_HOST", config('custom.proxy_host'));

#[OA\Info(version: "0.1.0", title: "UNLaM Social Proxy", description: "Proxy for UNLaM Social API")]
#[OA\Server(url: API_HOST)]
class TuiterApiController
{
    private array $defaultResponseHeaders = [
        'Content-Type' => 'application/json',
    ];

    private $defaultRequestHeaders = [];

    private $host = '';

    public function __construct()
    {
        $this->host = config('custom.tuiter_host');
        $this->defaultRequestHeaders['Api-Secret'] = config('custom.tuiter_api_key');
    }


    #[OA\Post(path: '/api/v1/login')]
    #[OA\HeaderParameter(in: 'header', description: 'Application Token', schema: new OA\Schema(type: 'string'), name: 'Application-Token')]
    #[OA\Response(response: 200, description: 'User Created')]
    #[OA\Response(response: 400, description: 'Bad Request')]
    #[OA\RequestBody(description: 'User Data',
        required: true,
        content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'password', type: 'string'),
            ]
        ))]
    )]
    public function login(Request $request)
    {
        $res = Http::withHeaders($this->defaultRequestHeaders)->post(
            $this->host . '/v1/login',
            $request->all()
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }

    #[OA\Post(path: '/api/v1/users')]
    #[OA\HeaderParameter(in: 'header', description: 'Application Token', schema: new OA\Schema(type: 'string'), name: 'Application-Token')]
    #[OA\Response(response: 200, description: 'User Created')]
    #[OA\Response(response: 400, description: 'Bad Request')]
    #[OA\RequestBody(description: 'User Data',
        required: true,
        content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'password', type: 'string'),
            ]
        ))]
    )]
    public function createUser(Request $request)
    {
        $res = Http::withHeaders($this->defaultRequestHeaders)->post(
            $this->host . '/v1/users',
            $request->all()
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }

    #[OA\Get(path: '/api/v1/me/feed')]
    #[OA\HeaderParameter(in: 'header', description: 'User Token', schema: new OA\Schema(type: 'string'), name: 'Authorization')]
    #[OA\HeaderParameter(in: 'header', description: 'Application Token', schema: new OA\Schema(type: 'string'), name: 'Application-Token')]
    #[OA\Response(response: 200, description: 'Feed')]
    public function feed(Request $request)
    {
        $this->defaultRequestHeaders['Authorization'] = $request->header('Authorization');
        $res = Http::withHeaders($this->defaultRequestHeaders)->get(
            $this->host . '/v1/me/feed',
            $request->query()
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }

    #[OA\Get(path: '/api/v1/me/profile')]
    #[OA\HeaderParameter(in: 'header', description: 'User Token', schema: new OA\Schema(type: 'string'), name: 'Authorization')]
    #[OA\HeaderParameter(in: 'header', description: 'Application Token', schema: new OA\Schema(type: 'string'), name: 'Application-Token')]
    #[OA\Response(response: 200, description: 'Profile')]
    public function profile(Request $request)
    {
        $this->defaultRequestHeaders['Authorization'] = $request->header('Authorization');
        $res = Http::withHeaders($this->defaultRequestHeaders)->get(
            $this->host . '/v1/me/profile'
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }

    #[OA\Put(path: '/api/v1/me/profile')]
    #[OA\HeaderParameter(name: 'Application-Token', description: 'Application Token', in: 'header', schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'User Created')]
    #[OA\Response(response: 400, description: 'Bad Request')]
    #[OA\RequestBody(description: 'User Data',
        required: true,
        content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'avatar_url', type: 'string'),
                new OA\Property(property: 'password', type: 'string'),
            ]
        ))]
    )]
    public function updateProfile(Request $request)
    {
        $this->defaultRequestHeaders['Authorization'] = $request->header('Authorization');
        $res = Http::withHeaders($this->defaultRequestHeaders)->put(
            $this->host . '/v1/me/profile',
            $request->all()
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }

    #[OA\Post(path: '/api/v1/me/tuits')]
    #[OA\HeaderParameter(in: 'header', description: 'User Token', schema: new OA\Schema(type: 'string'), name: 'Authorization')]
    #[OA\HeaderParameter(in: 'header', description: 'Application Token', schema: new OA\Schema(type: 'string'), name: 'Application-Token')]
    #[OA\Response(response: 200, description: 'Tuit Created')]
    #[OA\RequestBody(description: 'Tuit Body',
        required: true,
        content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(
            properties: [
                new OA\Property(property: 'message', type: 'string'),
            ]
        ))]
    )]
    public function createTuit(Request $request)
    {
        $this->defaultRequestHeaders['Authorization'] = $request->header('Authorization');
        $res = Http::withHeaders($this->defaultRequestHeaders)->post(
            $this->host . '/v1/me/tuits',
            $request->all()
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }


    #[OA\Get(path: '/api/v1/me/tuits/{tuit_id}')]
    #[OA\HeaderParameter(in: 'header', description: 'User Token', schema: new OA\Schema(type: 'string'), name: 'Authorization')]
    #[OA\HeaderParameter(in: 'header', description: 'Application Token', schema: new OA\Schema(type: 'string'), name: 'Application-Token')]
    #[OA\Response(response: 200, description: 'Mostrar Tuit')]
    #[OA\Response(response: 401, description: 'Not Allowed')]
    public function showTuit(Request $request)
    {
        $this->defaultRequestHeaders['Authorization'] = $request->header('Authorization');
        $tuitId = $request->route('tuit_id');
        $res = Http::withHeaders([
            'Authorization' => $request->header('Authorization')
        ])->get(
            $this->host . '/v1/me/tuits/' . $tuitId
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }

    #[OA\Post(path: '/api/v1/me/tuits/{tuit_id}/likes')]
    #[OA\HeaderParameter(in: 'header', description: 'User Token', schema: new OA\Schema(type: 'string'), name: 'Authorization')]
    #[OA\HeaderParameter(in: 'header', description: 'Application Token', schema: new OA\Schema(type: 'string'), name: 'Application-Token')]
    #[OA\Response(response: 200, description: 'Like Added')]
    #[OA\Response(response: 401, description: 'Not Allowed')]
    public function addLike(Request $request)
    {
        $this->defaultRequestHeaders['Authorization'] = $request->header('Authorization');
        $tuitId = $request->route('tuit_id');
        $res = Http::withHeaders($this->defaultRequestHeaders)->post(
            $this->host . '/v1/me/tuits/' . $tuitId . '/likes'
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }

    #[OA\Delete(path: '/api/v1/me/tuits/{tuit_id}/likes')]
    #[OA\HeaderParameter(in: 'header', description: 'User Token', schema: new OA\Schema(type: 'string'), name: 'Authorization')]
    #[OA\HeaderParameter(in: 'header', description: 'Application Token', schema: new OA\Schema(type: 'string'), name: 'Application-Token')]
    #[OA\Response(response: 200, description: 'Like Removed')]
    #[OA\Response(response: 401, description: 'Not Allowed')]
    public function removeLike(Request $request)
    {
        $this->defaultRequestHeaders['Authorization'] = $request->header('Authorization');
        $tuitId = $request->route('tuit_id');
        $res = Http::withHeaders($this->defaultRequestHeaders)->delete(
            $this->host . '/v1/me/tuits/' . $tuitId . '/likes'
        );
        return response($res->body(), $res->status(), $this->defaultResponseHeaders);
    }

}
