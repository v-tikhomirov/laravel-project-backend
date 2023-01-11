<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Company;
use App\Models\ConnectionsHistory;
use App\Models\Matching;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function openConnect(Request $request): JsonResponse
    {
        $price = 1;
        $company_id = $request->get('company_id');
        $match_id = $request->get('match_id');

        $match = Matching::find($match_id);

        $company = Company::find($company_id);

        if ($match->company_id === $company->id) {
            $company->load(['balance']);
            $balance = $company->balance;
            if ($balance) {
                if ($balance->amount - $price < 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You don\'t have enough contacts points'
                    ]);
                }

                $balance->processPayment();
                ConnectionsHistory::create([
                    'amount' => 1,
                    'cv_id' => $match->cv_id,
                    'type' => ConnectionsHistory::TYPE_EXPENSE,
                    'company_id' => $company_id,
                    'user_id' => auth()->user()->id
                ]);
                $match->status = Matching::STATUS_COMPANY_INTERESTED;
                $match->save();

                return response()->json([
                    'success' => true,
                    'balance' => $balance
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong'
        ]);

    }
}
