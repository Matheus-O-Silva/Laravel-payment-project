<?php
declare(strict_types=1);

namespace App\Services;

use App\Repository\Eloquent\TransactionRepository;
use App\Repository\Eloquent\UserRepository;
use App\Repository\Eloquent\BalanceRepository;
use App\Services\UserService;
use App\Services\BalanceService;
use Illuminate\Support\Facades\DB;
use Exception;

class TransactionService
{
    private $transactionRepository;
    private $userRepository;
    private $userService;
    private $balanceRepository;
    private $balanceService;

    /**
     * construct
     *
     * @param App\Repository\Eloquent\TransactionRepository $transactionRepository
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        UserRepository $userRepository,
        BalanceRepository $balanceRepository,
        UserService $userService,
        BalanceService $balanceService
    )

    {
        return $this->transactionRepository = $transactionRepository;
        return $this->userRepository        = $userRepository;
        return $this->userService           = $userService;
        return $this->balanceService        = $balanceService;
    }

    /**
     * Register new User
     *
     * @throws \Exception
     * @return Json
     */
    public function sendMoney($sent_user_id, $receiveDocumentNumber, $amount)
    {
        //if the user has enought balance, continue the operation
        if($this->balanceService->hasBalance($sent_user_id, $amount)){

            $user          = $this->userRepository->findById($sent_user_id);
            $receivingUser = $this->userRepository->findByDocumentNumber($receiveDocumentNumber);

            //verify if the authenticated user can do transfers
            if($user->hasRole(['shopKeeper'])){
                return response('Insufficient permissions', 500);
            }

            //verify if the authenticated has permissions
            if(!$this->userRepository->hasPermissions($user->documentNumber,['send_money'])){
                return response('Insufficient permissions', 500);
            }

            //Do the Transaction
            $this->doTransaction($user, $receivingUser, $amount);

        }else{
            return response()->json('Insufficient balance', 500);
        }
    }

    /**
     * do transaction
     *
     * @throws \Exception
     * @return Json
     */
    public function doTransaction($send, $to, $amount)
    {
        try{
            DB::beginTransaction();
            //remove amount from the send user
            $sentUser = $this->balanceRepository->findByUserId($send->id);
            $sentUser->update('amount', $sentUser->amount - $amount);
            $sentUser->refresh();
            $sentUser->save();

            //add amount to the receiving user
            $receivingUser = $this->balanceRepository->findByUserId($to->id);
            $receivingUser->update('amount', $receivingUser->amount + $amount);
            $receivingUser->refresh();
            $receivingUser->save();

            //save the Operation to the transactions log
            $this->transactionRepository->create([
                'sent_user_id'       => $send->id,
                'receive_user_id'    => $to->id,
                'transferred_amount' => $amount
            ]);

            DB::commit();

            //send email

        }catch(Exception $e){
            DB::rollback();
            throw new Exception("Erro na transação.Por favor, tente novamente mais tarde", 1);
        }
    }

    /**
     * send an email to notify the transfer
     *
     * @throws \Exception
     * @return Json
     */
    public function notifyTransfer($send, $to, $amount)
    {

    }

}
