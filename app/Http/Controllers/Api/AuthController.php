<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
    protected $userService;

    /**
     * Make a instance of Service
     *
     * @param \App\Services\UserService  $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Create a new User
     *
     * @param \App\Http\Requests\RegisterRequest
     * @throws \Exception $e
     * @return \Symfony\Component\HttpFoundation\JsonResponse;
     */
    public function register(RegisterRequest $registerRequest) : JsonResponse
    {
        try {
            $user = $this->userService->store($registerRequest);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()
                ->json(
                    'cannot.perform.your.action.try.again.later',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
        }

        return new JsonResponse($user, Response::HTTP_CREATED);
    }

    /**
     * Responsible method for authenticate a user
     *
     * @param \App\Http\Requests\RegisterRequest
     * @throws \Exception $e
     * @return \Symfony\Component\HttpFoundation\JsonResponse;
     */
    public function login(LoginRequest $loginRequest) : JsonResponse
    {
        try {
            $user = $this->userService->authenticate($loginRequest);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()
                ->json(
                    'cannot.perform.your.action.try.again.later',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
        }

        return new JsonResponse($user, Response::HTTP_OK);
    }

    /**
    * Logout the current user and return a JSON response.
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function logout() : JsonResponse
    {
        try {
            $user = $this->userService->logout();
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()
                ->json(
                    'cannot.perform.your.action.try.again.later',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
        }

        return new JsonResponse($user, Response::HTTP_OK);
    }

}
