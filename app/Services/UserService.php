<?php
declare(strict_types=1);

namespace App\Services;

use App\Repository\Eloquent\UserRepository;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Balance;
use Illuminate\Support\Facades\Log;
use App\Models\Permission;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        try{
            DB::beginTransaction();

            $user = $this->userRepository->create([
                'name'           => $registerRequest->name,
                'role_id'        => $registerRequest->role_id == 'Comum' ? 2 : 1,
                'documentType'   => $registerRequest->documentType,
                'documentNumber' => $registerRequest->documentNumber,
                'email'          => $registerRequest->email,
                'device_number'  => $registerRequest->device_number,
                'password'       => Hash::make($registerRequest->password)
            ]);

            $sendMoneyPermission    = Permission::where('name','send_money')->first();
            $receiveMoneyPermission = Permission::where('name','receive_money')->first();

            if($registerRequest->role_id == 'Comum'){
                $user->permissions()->attach([$sendMoneyPermission->id, $receiveMoneyPermission->id]);
            }

            if($registerRequest->role_id == 'Lojista'){
                $user->permissions()->attach([$receiveMoneyPermission->id]);
            }

            Balance::create([
                'user_id' => $user->id,
                'amount'  => 0.00
            ]);

            DB::commit();
        }catch(Exception $e){
            Log::error($e->getMessage());
            DB::rollback();
        }

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
