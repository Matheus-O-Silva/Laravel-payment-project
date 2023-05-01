<?php
declare(strict_types=1);

namespace App\Repository\Eloquent;

use App\Models\Transaction;
use App\Repository\Contracts\TransactionRepositoryInterface;
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
     * @return Array
     */
    public function findAll(): array
    {
        return $this->transaction->get()->toArray();
    }

    /**
     * create a new transaction
     *
     * @param $data
     * @return Array
     */
    public function create($data): object
    {
        return $this->transaction->create($data);
    }

    /**
     * update a transaction
     *
     * @param $email
     * @param $data
     * @return Array
     */
    public function update(string $id,array $data): object
    {
        $transaction = $this->transaction->find($id);
        $transaction->update($data);

        $transaction->refresh();

        return $transaction;
    }

    /**
     * select a user by email
     *
     * @param $email
     * @return Array
     */
    public function find(string $email): ?object
    {
        return $this->transaction->where('email',$email)->first();
    }

    /**
     * delete a transaction
     *
     * @param $email
     * @return Array
     */
    public function delete(string $email): bool
    {
        if(!$transaction = $this->transaction->find($email)){
            throw new Exception("transaction Not Found", 1);
        }

        return $transaction->delete();
    }

    /**
     * returns registers with re Attributes
     *
     * @param $attributes
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function selectRelationAtribbutes($attributes) : Object
    {
       return $this->transaction->with($attributes)->get();
    }
}
