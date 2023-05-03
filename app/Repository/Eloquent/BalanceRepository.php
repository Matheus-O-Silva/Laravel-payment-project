<?php
declare(strict_types=1);

namespace App\Repository\Eloquent;

use App\Models\Balance;
use App\Repository\Contracts\BalanceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class BalanceRepository// implements BalanceRepositoryInterface
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
     * retrieves all transaction registers
     *
     * @param $attributes
     * @return Collection
     */
    public function findAll(): Collection
    {
        return $this->balance->get();
    }

    /**
     * create a new balance
     *
     * @param $data
     * @return object
     */
    public function create($data): object
    {
        return $this->balance->create($data);
    }

    /**
     * update a balance
     *
     * @param $id
     * @param $data
     * @return Collection
     */
    public function update(string $id,array $data): Collection
    {
        $balance = $this->balance->find($id);
        $balance->update($data);

        $balance->refresh();

        return $balance;
    }

    /**
     * select a balance by user_id
     *
     * @param $user_id
     * @return Collection
     */
    public function find($user_id)//: Collection
    {
        return $this->balance->where('user_id',$user_id)->first();
    }

    /**
     * delete a balance
     *
     * @param $id
     * @return Collection
     */
    public function delete($id): Collection
    {
        if(!$balance = $this->balance->find($id)){
            throw new Exception("Ballance Not Found");
        }

        return $balance->delete();
    }

    /**
     * returns registers with Attributes
     *
     * @param $attributes
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function selectRelationAtribbutes($attributes) : Collection
    {
       return $this->balance->with($attributes)->get();
    }
}
