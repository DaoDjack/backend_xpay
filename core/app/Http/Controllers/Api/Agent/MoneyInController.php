<?php

namespace App\Http\Controllers\Api;

use App\Models\Wallet;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Models\GatewayCurrency;
use App\Http\Controllers\Api\Common;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    use Common;

    public function methods()
    {
        $wallets = $this->gatewayWithLimit(auth()->user()->wallets);

        $notify[] = 'Payment Methods';
        return response()->json([
            'remark' => 'deposit_methods',
            'message' => ['success' => $notify],
            'data' => [
                'wallets' => $wallets
            ],
        ]);
    }

    public function gatewayWithLimit($wallets){
        foreach($wallets ?? [] as $wallet){

            $wallet->currency->gateways = $wallet->gateways();

            foreach($wallet->currency->gateways ?? [] as $gateway){
                $rate = $wallet->currency->rate;

                $min = $gateway->min_amount/$rate;
                $max = $gateway->max_amount/$rate;

                $gateway->deposit_min_limit = $min;
                $gateway->deposit_max_limit = $max;
            }
        }

        return $wallets;
    }

    public function depositInsert(Request $request)
    {
          // Depot via mobile money
         if(isset($request->getway) && $request->getway == "MOBILE MONEY"){


             $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|gt:0',
            'method_code' => 'required',
            'wallet_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }

        $user = auth()->user();
        $userType = $this->guard()['user_type'];

        $wallet = Wallet::where('user_id', $user->id)->where('user_type', $userType)->where('id', $request->wallet_id)->first();

        if(!$wallet) {
            $notify[] = 'Invalid wallet';
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ]);
        }

        $currency = Currency::enable()->where('currency_code', $wallet->currency_code)->first();
        if(!$currency) {
            $notify[] = 'Invalid gateway';
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ]);
        }

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->where('method_code', $request->method_code)->where('currency', $currency->currency_code)->first();

        if (!$gate) {
            $notify[] = 'Invalid gateway';
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ]);
        }

       /* if ($gate->min_amount / $currency->rate > $request->amount || $gate->max_amount / $currency->rate < $request->amount) {
            $notify[] =  'Please follow deposit limit';
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ]);
        }*/

        $charge = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable = $request->amount + $charge;
        $final_amo = $payable;


        //Mise Jour du depot : La transaction cinetPay s'est bien passé

        if($request-> statut == "true"){
        $data = new Deposit();
        $data->user_id = $user->id;
        $data->user_type = $userType;
        $data->wallet_id = $request->wallet_id;
        $data->currency_id = $currency->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = 1;
        $data->final_amo = $final_amo;
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->status = 1;
        $data->isCinetPayTransaction = 1;
        $data->transcationCinetPayId = $request->transactionId;
        $data->statutCinetPayTransaction = 1;
        $data->save();


          // Mise  jour du solde

        /*$id = $data->id;
        $deposit = Deposit::where('id', $id)->where('status', 0)->firstOrFail();*/


         if ($data->status == 1) {
           /* ;

            if($data->user_type == 'USER'){
                $user = User::find($deposit->user_id);
                $userType = 'USER';
            }
            elseif($deposit->user_type == 'AGENT'){
                $user = Agent::find($deposit->user_id);
                $userType = 'AGENT';
            }*/

            $userWallet = Wallet::find($data->wallet_id);
            $userWallet->balance += $data->amount;
            $userWallet->save();

            $transaction = new Transaction();
            $transaction->user_id = $data->id;

            $transaction->user_type = $data->user_type;
            $transaction->wallet_id = $userWallet->id;
            $transaction->currency_id = $data->currency_id;
            $transaction->before_charge = $data->amount;

            $transaction->amount = $data->amount;
            $transaction->post_balance = $userWallet->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Add money via ' . $data->gatewayCurrency()->name;
            $transaction->trx = $data->trx;
            $transaction->remark = 'add_money';
            $transaction->save();

            /*if (!$isManual) {
                $adminNotification = new AdminNotification();
                $adminNotification->user_type = $userType;
                $adminNotification->user_id = $user->id;
                $adminNotification->title = 'Deposit successful via '.$deposit->gatewayCurrency()->name;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }*/

           /* notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name' => $deposit->gatewayCurrency()->name,
                'method_currency' => $deposit->method_currency,
                'method_amount' => showAmount($deposit->final_amo, getCurrency($deposit->method_currency)),
                'amount' => showAmount($deposit->amount, $deposit->currency),
                'charge' => showAmount($deposit->charge, $deposit->currency),
                'currency' => $deposit->currency->currency_code,
                'rate' => showAmount($deposit->rate),
                'trx' => $deposit->trx,
                'post_balance' => showAmount($userWallet->balance, $deposit->currency)
            ]);*/

        }

        // Fin process Mise a jour du solde


         $notify[] =  'Deposit inserted';
        return response()->json([
            'remark'=>'deposit_inserted',
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[
                'deposit' => $data,
                'redirect_url' => route('deposit.app.confirm', encrypt($data->id))
            ]
        ]);
        }else{
        $data = new Deposit();
        $data->user_id = $user->id;
        $data->user_type = $userType;
        $data->wallet_id = $request->wallet_id;
        $data->currency_id = $currency->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = 1;
        $data->final_amo = $final_amo;
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->status = 3;
          $data->isCinetPayTransaction = 1;
        $data->transcationCinetPayId = $request->transactionId;
        $data->statutCinetPayTransaction = 0;
        $data->save();


       $notify[] =  'Deposit not  inserted';
          return response()->json([
            'remark'=>'deposit_notinserted',
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[
                'deposit' => $data,
                'redirect_url' => route('deposit.app.confirm', encrypt($data->id))
            ]
        ]);
        }

           // Fin depot via mobile money

         }else{
             $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|gt:0',
            'method_code' => 'required',
            'wallet_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }

        $user = auth()->user();
        $userType = $this->guard()['user_type'];

        $wallet = Wallet::where('user_id', $user->id)->where('user_type', $userType)->where('id', $request->wallet_id)->first();

        if(!$wallet) {
            $notify[] = 'Invalid wallet';
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ]);
        }

        $currency = Currency::enable()->where('currency_code', $wallet->currency_code)->first();
        if(!$currency) {
            $notify[] = 'Invalid gateway';
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ]);
        }

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->where('method_code', $request->method_code)->where('currency', $currency->currency_code)->first();

        if (!$gate) {
            $notify[] = 'Invalid gateway';
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ]);
        }

       /* if ($gate->min_amount / $currency->rate > $request->amount || $gate->max_amount / $currency->rate < $request->amount) {
            $notify[] =  'Please follow deposit limit';
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ]);
        }*/

        $charge = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable = $request->amount + $charge;
        $final_amo = $payable;
        $data = new Deposit();
        $data->user_id = $user->id;
        $data->user_type = $userType;
        $data->wallet_id = $request->wallet_id;
        $data->currency_id = $currency->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = 1;
        $data->final_amo = $final_amo;
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->save();



             // Mise  jour du solde

             if ($data->status == 1) {

                 $userWallet = Wallet::find($data->wallet_id);
                 $userWallet->balance += $data->amount;
                 $userWallet->save();

                 $transaction = new Transaction();
                 $transaction->user_id = $data->id;

                 $transaction->user_type = $data->user_type;
                 $transaction->wallet_id = $userWallet->id;
                 $transaction->currency_id = $data->currency_id;
                 $transaction->before_charge = $data->amount;

                 $transaction->amount = $data->amount;
                 $transaction->post_balance = $userWallet->balance;
                 $transaction->charge = 0;
                 $transaction->trx_type = '+';
                 $transaction->details = 'Add money via ' . $data->gatewayCurrency()->name;
                 $transaction->trx = $data->trx;
                 $transaction->remark = 'add_money';
                 $transaction->save();
             }

             // Fin process Mise a jour du solde
        

        $notify[] =  'Deposit inserted X-PAY CASH';
        return response()->json([
            'remark'=>'deposit_inserted',
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[
                'deposit' => $data,
                'redirect_url' => route('deposit.app.confirm', encrypt($data->id))
            ]
        ]);
         }

         }



    }

