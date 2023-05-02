<?php
declare(strict_types=1);

namespace App\Repository\Eloquent;

use App\Models\Balance;
use App\Repository\Contracts\BalanceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class BalanceRepository implements BalanceRepositoryInterface
{
    protected $balance;

    /**
     * Construct
     *
     * @param \App\Models\Balance  $balance
     */
    public function __construct(Balance $balance)
    {
        $this->balance = $balance;
    }

    /**
     * retrieves all balance registers
     *
     * @param $attributes
     * @return Array
     */
    public function findAll(): array
    {
        return $this->balance->get()->toArray();
    }

    /**
     * create a new balance
     *
     * @param $data
     * @return Array
     */
    public function create($data): object
    {
        return $this->balance->create($data);
    }

    /**
     * update a balance
     *
     * @param $email
     * @param $data
     * @return Array
     */
    public function update(string $id,array $data): object
    {
        $balance = $this->balance->find($id);
        $balance->update($data);

        $balance->refresh();

        return $balance;
    }

    /**
     * select a user by id
     *
     * @param $user_id
     * @return Array
     */
    public function find(string $id): ?object
    {
        return $this->balance->where('id',$id)->first();
    }

    /**
     * select a user by user_id
     *
     * @param $user_id
     * @return Collection
     */
    public function findByUserId($user_id): Collection
    {
        $user = $this->balance->where('user_id',$user_id)->first();

        if(!$user){
            throw new Exception("User Not Found", 1);
        }

        return $user;
    }

    /**
     * delete a balance
     *
     * @param $email
     * @return Array
     */
    public function delete(string $email): bool
    {
        if(!$balance = $this->balance->find($email)){
            throw new Exception("balance Not Found", 1);
        }

        return $balance->delete();
    }

    /**
     * returns registers with re Attributes
     *
     * @param $attributes
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function selectRelationAtribbutes($attributes) : Object
    {
       return $this->balance->with($attributes)->get();
    }

    public function getBalanceByUserId($user_id)
    {
        $userAmount = $this->balance->select('amount')->where('user_id',$user_id)->get();

        $userAmount = $userAmount[0]->amount;

        return $userAmount;
    }
}
