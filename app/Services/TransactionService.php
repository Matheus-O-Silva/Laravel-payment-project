<?php
declare(strict_types=1);

namespace App\Services;

use App\Repository\Eloquent\TransactionRepository;
use App\Repository\Eloquent\UserRepository;
use App\Repository\Eloquent\BalanceRepository;
use App\Models\Transaction;
use App\Services\UserService;
use App\Services\BalanceService;
use App\Services\EmailService;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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
        return $this->balanceRepository     = $balanceRepository;
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
    public function sendMoney($sent_user_id, $receivingUserDocumentNumber, $amount)
    {
        $userAmount = Balance::where('user_id', $sent_user_id)->first();
        if(empty($userAmount->amount)){
            throw new Exception("Insufficient balance");
        }

        $userAmount = $userAmount->amount;

        //if the user has enough balance, continue the operation
        if($userAmount >= $amount){
            $user          = User::find($sent_user_id);
            $receivingUser = User::where('documentNumber',$receivingUserDocumentNumber)->get();
            if(empty($receivingUser)){
                throw new Exception("User Not Found");
            }

            //verify if the authenticated user can do transfers
            if($user->hasRole('shopKeeper')){
                throw new Exception("Insufficient permissions");
            }

            //verify if the authenticated has permissions
            if (!$user->hasPermissions(['send_money'])) {
                throw new Exception("Insufficient permissions");
            }

            //Do the Transaction
            $this->doTransaction($user, $receivingUser, $amount);

        }else{
            throw new Exception("Saldo insuficiente para realizar a transferência");
        }
    }

    /**
     * do transaction
     *
     * @throws \Exception
     * @return bool
     */
    public function doTransaction($send, $to, $amount): bool
    {
        if ($amount <= 0) {
            throw new Exception("Valor inválido para transferência");
        }

        try{
            DB::beginTransaction();
            $senderUser = Balance::where('user_id', $send->id)->first();

            if ($senderUser->amount < $amount) {
                DB::rollback();
                throw new Exception("Saldo insuficiente para realizar a transferência");
            }

            if ($senderUser->amount < $amount) {
                DB::rollback();
                throw new Exception("Saldo insuficiente para realizar a transferência");
            }
            $senderUser->update(['amount' => $senderUser->amount - $amount]);
            $senderUser->refresh();
            $senderUser->save();

            //add amount to the receiving user
            $receivingUser = Balance::where('user_id', $to[0]->id)->first();

            $receivingUser->update(['amount' => $receivingUser->amount + $amount]);
            $receivingUser->refresh();
            $receivingUser->save();

            //save the Operation to the transactions log
            Transaction::create([
                'sent_user_id'       => $send->id,
                'receive_user_id'    => $to[0]->id,
                'transferred_amount' => $amount
            ]);

            if(!$this->sendNotificationEmail()){
                DB::rollback();
                throw new Exception("Erro na transação. Por favor, tente novamente mais tarde");
            }

            if(!$this->AllowVerify()){
                DB::rollback();
                throw new Exception("Erro na transação. Por favor, tente novamente mais tarde");
            }

            DB::commit();
            return true;

        }catch(Exception $e){
            DB::rollback();
            throw new Exception("Erro na transação. Por favor, tente novamente mais tarde");
        }

        return false;
    }

    public function sendNotificationEmail(): bool
    {
        $response = Http::get('https://run.mocky.io/v3/4ce65eb0-2eda-4d76-8c98-8acd9cfd2d39');

        if ($response->successful()) {
            return true;
        } else {
           return false;
        }
    }

    public function AllowVerify(): bool
    {
        $response = Http::get('https://run.mocky.io/v3/f2fe9a2d-090f-4129-b9bf-70d283c97d5c');

        if ($response->successful()) {
            return true;
        } else {
           return false;
        }
    }

}
