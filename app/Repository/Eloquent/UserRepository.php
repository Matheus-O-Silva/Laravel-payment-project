<?php
declare(strict_types=1);

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\Contracts\UserRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    protected $user;

    /**
     * Construct
     *
     * @param \App\Models\User  $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * retrieves all user registers
     *
     * @param $attributes
     * @return Array
     */
    public function findAll(): array
    {
        return $this->user->get()->toArray();
    }

    /**
     * create a new user
     *
     * @param $data
     * @return Array
     */
    public function create($data): object
    {
        return $this->user->create($data);
    }

    /**
     * update a user
     *
     * @param $email
     * @param $data
     * @return Array
     */
    public function update(string $email,array $data): object
    {
        $user = $this->user->find($email);
        $user->update($data);

        $user->refresh();

        return $user;
    }

    /**
     * delete a user
     *
     * @param $email
     * @return Array
     */
    public function delete(string $email): bool
    {
        if(!$user = $this->user->find($email)){
            throw new Exception("User Not Found", 1);
        }

        return $user->delete();
    }

    /**
     * select a user by documentNumber
     *
     * @param $documentNumber
     * @return Array
     */
    public function find(string $documentNumber): ?object
    {
        return $this->user->where('documentNumber',$documentNumber)->first();
    }

    /**
     * select a user by id
     *
     * @param $id
     * @return Collection
     */
    public function findById($id): Collection
    {
        $user = $this->user->find($id);
        if(!$user){
            throw new Exception("User Not Found", 1);
        }

        return $user;
    }

    /**
     * select a user by id
     *
     * @param $id
     * @return Collection
     */
    public function findByDocumentNumber($documentNumber): Collection
    {
        $user = $this->user->where('documentNumber',$documentNumber);
        if(!$user){
            throw new Exception("User Not Found", 1);
        }

        return $user;
    }

    /**
     * returns registers with re Attributes
     *
     * @param $attributes
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function selectRelationAtribbutes($attributes) : Object
    {
       return $this->user->with($attributes)->get();
    }

    public function hasRole(string $documentNumber, String $role): bool
    {
        return $this->user->role()->where('documentNumber', $documentNumber)->where('name', $role)->exists();
    }

    public function hasPermissions(string $documentNumber, Array $permissions): bool
    {
        $userPermissions = $this->user->permissions()->where('documentNumber', $documentNumber)->pluck('name')->toArray();

        foreach ($permissions as $permission) {
            if (!in_array($permission, $userPermissions)) {
                return false;
            }
        }

        return true;
    }
}
