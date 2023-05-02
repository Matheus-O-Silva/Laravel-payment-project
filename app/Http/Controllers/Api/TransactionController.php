<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class TransactionController extends Controller
{
    protected $transactionService;

    /**
     * Make a instance of Service
     *
     * @param \App\Services\TransactionService  $transactionService
     */
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Sent amount to other user
     *
     * @param \Illuminate\Http\Request;
     * @throws \Exception $e
     * @return \Symfony\Component\HttpFoundation\JsonResponse;
     */
    public function sendMoney(Request $request)// : JsonResponse
    {
        try {
            $this->transactionService->sendMoney($request->sent_user_id, $request->receivingUserDocumentNumber, $request->amount);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()
                ->json(
                    'cannot.perform.your.action.try.again.later',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
        }

        return new JsonResponse('success', Response::HTTP_OK);
    }


}
