<?php

namespace App\Http\Controllers\Api;

use App\Models\Wallet;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\Airtime;
use App\Models\User;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Models\GatewayCurrency;
use App\Http\Controllers\Api\Common;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AirtimeController extends Controller
{
    use Common;
    
     public function historiqueTransactionAirtime()
    { 
     
      $airtime = Airtime::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();
      
       // Notification
        $notify[] =  'historique Transaction Airtime';
         return response()->json([
        'remark'=>'all airtime transactions',
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[$airtime]
            ]);
    }
    

    public function airtimeInsert(Request $request)
    { 
        
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

       /* $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->where('method_code', $request->method_code)->where('currency', $currency->currency_code)->first();

        if (!$gate) {
            $notify[] = 'Invalid gateway';
            return response()->json([
                'remark'=>'validation_error',
                'status'=>'error',
                'message'=>['error'=>$notify],
            ]);
        }*/
        
    
        // Insertion dans la table Airtimes
        $data = new Airtime();
        $data->user_id = $user->id;
        $data->user_type = $userType;
        $data->wallet_id = $request->wallet_id;
        $data->currency_id = $currency->id;
        $data->method_code = $request->method_code; 
        $data->method_currency = strtoupper( $wallet->currency_code);
        $data->country_name = $request->country;
        $data->receiver_number = $request->receiver_number;
        $data->amount = $request->amount;
        $data->operateur = $request->operateur;
        $data->flagoperateur = $request->flagoperateur;
        $data->transaction_id = $request->transactionId;
        $data->status = 1;
        $data->save();
        
        
        
        // Inscription dans la table transaction et mise a jour du wallet
           $userWallet = Wallet::find($data->wallet_id);
            $userWallet->balance -= $data->amount;
            $userWallet->save();
             
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->user_type = $userType;
            $transaction->wallet_id = $userWallet->id;
            $transaction->currency_id = $currency->id;
            $transaction->before_charge = $data->amount;
            $transaction->amount = $data->amount;
            $transaction->post_balance = $userWallet->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = 'Buy Airtimes';
            $transaction->receiver_number = $request->receiver_number;
            $transaction->trx = $data->transaction_id;
            $transaction->remark = 'buy_airtimes';
            $transaction->save();
            
            // Ajout de la commission
                $montantCommission = $request->amount*2/100;
                $userWallet->balance +=$montantCommission;
                $userWallet->save();
                
                
                $userCommission = new Transaction();
                $userCommission->user_id = $user->id;
                $userCommission->user_type = $userType;
                $userCommission->wallet_id = $userWallet->id;
                $userCommission->currency_id = $currency->id;
                $userCommission->before_charge = $montantCommission;
                $userCommission->amount = $montantCommission;
                $userCommission->post_balance = $userWallet->balance;
                $userCommission->charge = 0;
                $userCommission->trx_type = '+';
                $userCommission->details = 'Commission Airtimes';
                $userCommission->receiver_number = $request->receiver_number;
                $userCommission->trx = $data->transaction_id;
                $userCommission->remark = 'commission_airtimes';
                $userCommission->save();
          
            
            
            
            
            
            // Notification
            $notify[] =  'Airime buy successfully';
           return response()->json([
            'remark'=>'Airime buy succes',
            'status'=>'success',
            'message'=>['success'=>$notify],
            'data'=>[
                'deposit' => $data,
                'redirect_url' => route('deposit.app.confirm', encrypt($data->id))
                ]
            ]);
            
            
         }
        
}

