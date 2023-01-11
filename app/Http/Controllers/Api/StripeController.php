<?php

namespace App\Http\Controllers\Api;

use App\Enum\ProductsPrices;
use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Company;
use App\Models\ConnectionsHistory;
use App\Models\Payment;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class StripeController extends Controller
{
    /**
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createSession($contactsAmount, $cvId): \Illuminate\Http\JsonResponse
    {
        if($contactsAmount == 1) {
            $price = env("STRIPE_PRICE_ID_1");
        } else if($contactsAmount == 40) {
            $price = env("STRIPE_PRICE_ID_40");
        } else if($contactsAmount == 100) {
            $price = env("STRIPE_PRICE_ID_100");
        } else {
            $price = env("STRIPE_PRICE_ID_1");
        }

        Stripe::setApiKey(env("STRIPE_API_KEY"));

        $session = StripeSession::create([
            'line_items' => [[
                'price' => $price,
                'quantity' => 1,
            ]],
            "client_reference_id" => $cvId,
            'mode' => 'payment',
            'success_url' => env("STRIPE_SUCCESS_URL"),
            'cancel_url' => env("STRIPE_CANCEL_URL"),
        ]);

        if($data['session_id'] = $session->id) {
            $data['company_id'] = auth()->user()->companies[0]->id;
            $data['amount'] = $session->amount_subtotal / 100;
            $data['status'] = $session->payment_status;

            if($data['amount'] == ProductsPrices::PRICE_FOR_1->value) {
                $data['contacts_amount'] = 1;
            } else if($data['amount'] == ProductsPrices::PRICE_FOR_40->value) {
                $data['contacts_amount'] = 40;
            } else if($data['amount'] == ProductsPrices::PRICE_FOR_100->value) {
                $data['contacts_amount'] = 100;
            }
            Payment::create($data);
        }

        return response()->json(['session' => $session]);
    }

    public function checkCompanyPayments(): \Illuminate\Http\JsonResponse
    {
        $companyId = auth()->user()->companies[0]->id;
        $companyPayments = Payment::where('company_id', $companyId)->where('status', 'unpaid')->get();
        if($companyPayments) {
            Stripe::setApiKey(env("STRIPE_API_KEY"));
            foreach ($companyPayments as $companyPayment) {
                $session = StripeSession::retrieve($companyPayment->session_id);
                if($session->payment_status === "paid") {
                    $companyPayment->status = "paid";
                    $companyPayment->save();
                    $balance = auth()->user()->companies[0]->balance;
                    $balance->amount += $companyPayment->contacts_amount;
                    $balance->save();
                    ConnectionsHistory::create([
                        'amount' => $companyPayment->contacts_amount,
                        'type' => ConnectionsHistory::TYPE_REFILL,
                        'company_id' => $companyId,
                        'user_id' => auth()->user()->id
                    ]);
                }
            }
        }
        return response()->json(['success' => true, 'cv_id' => $session->client_reference_id]);
    }
}
