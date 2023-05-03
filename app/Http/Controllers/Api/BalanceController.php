<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BalanceService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class BalanceController extends Controller
{
    protected $balanceService;

    /**
     * Make a instance of Service
     *
     * @param \App\Services\BalanceService  $balanceService
     */
    public function __construct(balanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    /**
     * add amount to user account
     *
     * @param \Illuminate\Http\Request;
     * @throws \Exception $e
     * @return \Symfony\Component\HttpFoundation\JsonResponse;
     */
    public function addMoney(Request $request) : JsonResponse
    {
        try {
            $this->balanceService->addMoney(Auth::user()->id, $request->amount);
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

     /**
     * retrives the amount of the user account
     *
     * @param \Illuminate\Http\Request;
     * @throws \Exception $e
     * @return \Symfony\Component\HttpFoundation\JsonResponse;
     */
    public function getAmount(Request $request) : JsonResponse
    {
        $amount = $this->balanceService->getAmount(Auth::user()->id, $request->amount);
        try {
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()
                ->json(
                    'cannot.perform.your.action.try.again.later',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
        }

        return new JsonResponse($amount, Response::HTTP_OK);
    }


}
