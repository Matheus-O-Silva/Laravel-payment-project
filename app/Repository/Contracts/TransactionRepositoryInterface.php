<?php
declare(strict_types=1);

namespace App\Repository\Contracts;

use Illuminate\Database\Eloquent\Collection;
interface TransactionRepositoryInterface
{
    public function findAll(): Collection;
    public function create(array $data): object;
    public function update(string $email, array $data):Collection;
    public function delete(string $email): Collection;
    public function find(string $email): Collection;
}
