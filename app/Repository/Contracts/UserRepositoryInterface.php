<?php
declare(strict_types=1);

namespace App\Repository\Contracts;

use PhpParser\Node\Expr\Cast\Object_;

interface UserRepositoryInterface
{
    public function findAll(): array;
    public function create(array $data): object;
    public function update(string $email, array $data): object;
    public function delete(string $email): bool;
    public function find(string $email): ?object;
}
