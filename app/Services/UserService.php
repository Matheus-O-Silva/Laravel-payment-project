<?php
declare(strict_types=1);

namespace App\Services;

use App\Repository\Eloquent\UserRepository;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Response;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Exception;

class UserService
{

    private $userRepository;

    /**
     * construct
     *
     * @param App\Repository\Eloquent\UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        return $this->userRepository = $userRepository;
    }

    /**
     * Register new User
     *
     * @throws \Exception
     * @return \App\Http\Resources\UserResource
     */
    public function store(RegisterRequest $registerRequest): UserResource
    {
        $user = $this->userRepository->create([
            'name'           => $registerRequest->name,
            'documentType'   => $registerRequest->documentType,
            'documentNumber' => $registerRequest->documentNumber,
            'email'          => $registerRequest->email,
            'device_number'  => $registerRequest->device_number,
            'password'       => Hash::make($registerRequest->password)
        ]);

        return new UserResource($user);
    }

    /**
     * Authenticate a user
     *
     * @throws \Exception
     * @return \App\Http\Resources\UserResource
     */
    public function authenticate(LoginRequest $loginRequest): UserResource
    {
        $user = User::where('email', $loginRequest->email)->first();

        if (! $user || ! Hash::check($loginRequest->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if($loginRequest->has('logout_others_devices')){
            $user->tokens()->delete();
        }

        return new UserResource([$user->createToken($loginRequest->device_name)->plainTextToken]);
    }

    /**
    * Invalidate the access token of the authenticated user and delete all its tokens from the database.
    *
    * @throws \Exception
    * @return void
    */
    public function logout(): void
    {
        auth()->user()->tokens()->delete();
    }
}
