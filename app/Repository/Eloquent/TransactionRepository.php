<?php
declare(strict_types=1);

namespace App\Repository\Eloquent;

use App\Models\Transaction;
use App\Repository\Contracts\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class TransactionRepository implements TransactionRepositoryInterface
{
    protected $transaction;

    /**
     * Construct
     *
     * @param \App\Models\Transaction  $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * retrieves all transaction registers
     *
     * @param $attributes
     * @return Collection
     */
    public function findAll(): Collection
    {
        return $this->transaction->get();
    }

    /**
     * create a new transaction
     *
     * @param $data
     * @return object
     */
    public function create($data): object
    {
        return $this->transaction->create($data);
    }

    /**
     * update a transaction
     *
     * @param $id
     * @param $data
     * @return Collection
     */
    public function update(string $id,array $data): Collection
    {
        $transaction = $this->transaction->find($id);
        $transaction->update($data);

        $transaction->refresh();

        return $transaction;
    }

    /**
     * select a transaction by user_id
     *
     * @param $user_id
     * @return Collection
     */
    public function find($user_id): Collection
    {
        return $this->transaction->where('email',$user_id)->first();
    }

    /**
     * delete a transaction
     *
     * @param $id
     * @return Collection
     */
    public function delete($id): Collection
    {
        if(!$transaction = $this->transaction->find($id)){
            throw new Exception("transaction Not Found");
        }

        return $transaction->delete();
    }

    /**
     * returns registers with Attributes
     *
     * @param $attributes
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function selectRelationAtribbutes($attributes) : Collection
    {
       return $this->transaction->with($attributes)->get();
    }
}
