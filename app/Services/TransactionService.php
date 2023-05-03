<?php
declare(strict_types=1);

namespace App\Services;

use App\Repository\Eloquent\TransactionRepository;
use App\Repository\Eloquent\UserRepository;
use App\Repository\Eloquent\BalanceRepository;
use App\Models\Transaction;
use App\Models\Balance;
use Illuminate\Support\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Exception;

class TransactionService
{
    private $transactionRepository;
    private $balanceRepository;

    /**
     * construct
     *
     * @param App\Repository\Eloquent\BalanceRepository $balanceRepository
     * @param App\Repository\Eloquent\TransactionRepository $transactionRepository
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        BalanceRepository $balanceRepository,
    )
    {
        $this->transactionRepository = $transactionRepository;
        $this->balanceRepository     = $balanceRepository;
    }

    /**
     * Register new User
     *
     * @throws \Exception
     * @return Json
     */
    public function sendMoney($sent_user_id, $receivingUserDocumentNumber, $amount)
    {
        $userAmount = $this->balanceRepository->find($sent_user_id);

        $userAmount = $userAmount->amount;

        //if the user has enough balance, continue the operation
        if($userAmount >= $amount){
            $user          = User::find($sent_user_id);
            $receivingUser = User::where('documentNumber',$receivingUserDocumentNumber)->first();

            if($user->id == $receivingUser->id){
                throw new Exception("Can't send cash to yourself");
            }

            if(!$receivingUser){
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
            throw new Exception("Insufficient Balance");
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
            throw new Exception("Insufficient Balance");
        }

        try{
            DB::beginTransaction();
            $senderUser = $this->balanceRepository->find($send->id);

            if ($senderUser->amount < $amount) {
                DB::rollback();
                throw new Exception("Insufficient Balance");
            }

            if ($senderUser->amount < $amount) {
                DB::rollback();
                throw new Exception("Insufficient Balance");
            }
            $senderUser->update(['amount' => $senderUser->amount - $amount]);
            $senderUser->refresh();
            $senderUser->save();

            //add amount to the receiving user
            $receivingUser = Balance::where('user_id', $to->id)->first();

            $receivingUser->update(['amount' => $receivingUser->amount + $amount]);
            $receivingUser->refresh();
            $receivingUser->save();

            //save the Operation to the transactions log
            $this->transactionRepository->create([
                'sent_user_id'       => $send->id,
                'receive_user_id'    => $to->id,
                'action'             => "Transferência para $to->name",
                'transferred_amount' => $amount
            ]);

            if(!$this->sendNotificationEmail()){
                DB::rollback();
                throw new Exception("can't do this action. please, try again later");
            }

            if(!$this->AllowVerify()){
                DB::rollback();
                throw new Exception("can't do this action. Por favor, try again later");
            }

            DB::commit();
            return true;

        }catch(Exception $e){
            DB::rollback();
            throw new Exception("can't do this action. Por favor, try again later");
        }

        return false;
    }

    public function getTransactions($userId)
    {
        $userTransactions = Transaction::where('sent_user_id', $userId)->get()->map(function ($transaction) {
            $transaction->created_at_formatted = Carbon::parse($transaction->created_at)->format('d/m/Y H:i:s');
            return $transaction;
        });

        return $userTransactions;
    }

    public function getReceivings($userId)
    {
        $userTransactions = Transaction::where('receive_user_id', $userId)->get()->map(function ($transaction) {
            $transaction->created_at_formatted = Carbon::parse($transaction->created_at)->format('d/m/Y H:i:s');
            return $transaction;
        });

        return $userTransactions;
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
