<?php
declare(strict_types=1);

namespace App\Services;

use App\Repository\Eloquent\BalanceRepository;
use App\Repository\Eloquent\UserRepository;
use App\Services\UserService;
use Exception;

class BalanceService
{
    private $balanceService;
    private $userRepository;
    private $userService;
    private $balanceRepository;

    /**
     * construct
     *
     * @param App\Repository\Eloquent\BalanceService $balanceService
     */
    public function __construct(
        BalanceRepository $balanceService,
        UserService $userService,
        UserRepository $userRepository,
        BalanceRepository $balanceRepository
        )
    {
        return $this->balanceService        = $balanceService;
        return $this->userRepository        = $userRepository;
        return $this->userService           = $userService;
        return $this->balanceRepository     = $balanceRepository;
    }

    /**
     * Verify if the user has Balance to transfer
     *
     * @throws \Exception
     * @return Json
     */
    public function hasBalance($user_id, $amount): bool
    {
        $userBalance = $this->balanceRepository->find($user_id);

        return $userBalance > $amount ? true : false;
    }

}
