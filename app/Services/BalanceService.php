<?php
declare(strict_types=1);

namespace App\Services;

use App\Repository\Eloquent\BalanceRepository;
use App\Repository\Eloquent\UserRepository;
use App\Services\UserService;
use App\Models\Balance;
use Exception;

class BalanceService
{
    private $userRepository;
    private $userService;
    private $balanceRepository;

    /**
     * construct
     *
     * @param App\Repository\Eloquent\BalanceService $balanceService
     */
    public function __construct(
        UserService $userService,
        UserRepository $userRepository,
        BalanceRepository $balanceRepository
        )
    {
        $this->userRepository        = $userRepository;
        $this->userService           = $userService;
        $this->balanceRepository     = $balanceRepository;
    }

    /**
     * Verify if the user has Balance to transfer
     *
     * @throws \Exception
     * @return Json
     */
    public function hasBalance($user_id, $amount): bool
    {
        $userBalance = Balance::where('user_id', $user_id)->get();

        return $userBalance->balance >= $amount;
    }

}
