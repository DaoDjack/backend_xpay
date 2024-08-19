<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransactionCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionChargeController extends Controller{

    public function manageCharges(){   

        $pageTitle = "Transaction Charges";
        $charges = TransactionCharge::cursor();
       
        $temoin = DB::select("select * from transaction_charges where slug = 'money_transfer' order by min_limit ");
        foreach($temoin as $mon)
        {
            $data['value']= $mon->id;
            $data['label']= "De ".ceil($mon->min_limit).' à '.ceil($mon->max_limit);
            $moneyList[]=$data;
        }
        $moneyTransfer = $temoin[0];

        $temoin = DB::select("select * from transaction_charges where slug = 'invoice_charge' order by min_limit ");
        foreach($temoin as $mon)
        {
            $data['value']= $mon->id;
            $data['label']= "De ".ceil($mon->min_limit).' à '.ceil($mon->max_limit);
            $invoiceList[]=$data;
        }
        $invoiceCharge = $temoin[0];

        $temoin = DB::select("select * from transaction_charges where slug = 'exchange_charge' order by min_limit ");
        foreach($temoin as $mon)
        {
            $data['value']= $mon->id;
            $data['label']= "De ".ceil($mon->min_limit).' à '.ceil($mon->max_limit);
            $exchangeList[]=$data;
        }
        $exchangeCharge = $temoin[0];

        $temoin = DB::select("select * from transaction_charges where slug = 'api_charge' order by min_limit ");
        foreach($temoin as $mon)
        {
            $data['value']= $mon->id;
            $data['label']= "De ".ceil($mon->min_limit).' à '.ceil($mon->max_limit);
            $apiList[]=$data;
        }
        $apiCharge = $temoin[0];
        
        $temoin = DB::select("select * from transaction_charges where slug = 'voucher_charge' order by min_limit ");
        foreach($temoin as $mon)
        {
            $data['value']= $mon->id;
            $data['label']= "De ".ceil($mon->min_limit).' à '.ceil($mon->max_limit);
            $voucherList[]=$data;
        }
        $voucherCharge = $temoin[0];

        $moneyOutCharge =  $charges->where('slug', 'money_out_charge')->first();
        $temoin = DB::select("select * from transaction_charges where slug = 'money_out_charge' order by min_limit ");
        foreach($temoin as $mon)
        {
            $data['value']= $mon->id;
            $data['label']= "De ".ceil($mon->min_limit).' à '.ceil($mon->max_limit);
            $moneyOutList[]=$data;
        }
        $moneyOutCharge = $temoin[0];

        
        $temoin = DB::select("select * from transaction_charges where slug = 'money_in_charge' order by min_limit ");
        foreach($temoin as $mon)
        {
            $data['value']= $mon->id;
            $data['label']= "De ".ceil($mon->min_limit).' à '.ceil($mon->max_limit);
            $moneyInList[]=$data;
        }
        $moneyInCharge = $temoin[0];

        $temoin = DB::select("select * from transaction_charges where slug = 'make_payment' order by min_limit ");
        foreach($temoin as $mon)
        {
            $data['value']= $mon->id;
            $data['label']= "De ".ceil($mon->min_limit).' à '.ceil($mon->max_limit);
            $paymentList[]=$data;
        }
        $paymentCharge = $temoin[0];

        return view('admin.transaction_charges', compact('invoiceList','exchangeList','moneyOutList','moneyInList','apiList','paymentList','pageTitle', 'moneyTransfer','moneyList', 'invoiceCharge', 'exchangeCharge', 'apiCharge', 'voucherCharge', 'moneyTransfer','voucherList', 'moneyOutCharge', 'moneyInCharge', 'paymentCharge'));
    }
    public function detailCharges(Request $request){   

        $id = $_REQUEST['id'];
        $return['error']=1;
        $temoin = DB::select("select * from transaction_charges where id = '$id' ");
        if(!empty($temoin))
        {
            $return['error']=0;
            $return['data']=$temoin[0];
        }
        else  $return['message']="informations introuvables";
        header("Content-Type:application/json");
        echo json_encode($return);
        exit();
        
    }
    public function deleteCharges(Request $request){   

        $id = $_REQUEST['id'];
        $return['error']=1;
        $temoin = DB::select("select * from transaction_charges where id = '$id' ");
        if(!empty($temoin))
        {
            $test = $temoin[0];
            $temoin = DB::select("select * from transaction_charges where slug = '".$test->slug."' limit 2 ");
            if(count($temoin)>1)
            {
                $temoin = DB::select("delete from transaction_charges where id = '$id' ");
            }
        }
        
        header("Content-Type:application/json");
        echo json_encode($return);
        exit();
    }

    public function updateCharges(Request $request){
        
     
        $request->validate([
            'percent_charge' => 'numeric|between:0,100',
            'fixed_charge' => 'numeric|gte:0',
            'cap'   => 'numeric|gte:-1',
            'min_limit' => 'numeric|gte:0',
            'max_limit' => 'numeric|gt:min_limit',
            'monthly_limit' => 'numeric|gte:-1',
            'daily_limit' => 'numeric|gte:-1',
            'voucher_limit' => 'numeric|gte:-1',
            'agent_commission_fixed' => 'numeric|gte:0',
            'agent_commission_percent' => 'numeric|gte:0',
            'merchant_fixed_charge' => 'numeric|gte:0',
            'merchant_percent_charge' => 'numeric|gte:0',
        ]);
       
       
       
        if($_REQUEST['id']==0)
        {
           print_r($_REQUEST);
            $max = $_REQUEST['max_limit'];
            $min = $_REQUEST['min_limit'];
            
            $slug = $_REQUEST['slug'];

            $temoin = DB::select("select * from transaction_charges where slug = '$slug' and ((min_limit<=$min and max_limit>=$min) or (min_limit<=$max and max_limit>=$max))");
            
           
            if(!empty($temoin))
            {
                $notify[] = ['error', "Cet intervalle [$min - $max]   pour $slug chevauche avec un autre [".ceil($temoin[0]->min_limit)."-".ceil($temoin[0]->max_limit)."]"];
            }
            else
            {
                $champs = explode(',',"id,slug,fixed_charge,percent_charge,min_limit,max_limit,agent_commission_fixed,agent_commission_percent,merchant_fixed_charge,merchant_percent_charge,monthly_limit,daily_limit,daily_request_accept_limit,voucher_limit,cap");
               
                foreach($champs as $key)
                {
                    if(!empty($_REQUEST[$key])) $inputs[$key]=$_REQUEST[$key];
                }
                
               
                
                try {
                    $charge = TransactionCharge::create($inputs);
                } catch (\Exception $e) {
                    $notify[] = ['error', $e->getMessage()];
                    
                   
                    //return to_route(gatewayRedirectUrl())->withNotify($notify);
                }
            }
          
             //Verifier que l'interface ne coincide pas avec un autre.
            
        }
        else
        {
            $charge = TransactionCharge::findOrFail($request->id);
        

            $charge->daily_request_accept_limit = $request->daily_request_accept_limit;
    
            $charge->percent_charge = $request->percent_charge;
            $charge->fixed_charge = $request->fixed_charge;
            $charge->min_limit = $request->min_limit;
            $charge->max_limit = $request->max_limit;
            $charge->cap = $request->cap;
            $charge->agent_commission_fixed = $request->agent_commission_fixed;
            $charge->agent_commission_percent = $request->agent_commission_percent;
            $charge->merchant_fixed_charge = $request->merchant_fixed_charge;
            $charge->merchant_percent_charge = $request->merchant_percent_charge;
            $charge->monthly_limit = $request->monthly_limit;
            $charge->daily_limit = $request->daily_limit;
            $charge->voucher_limit = $request->voucher_limit;
            //exit();
            $charge->save(); 
        }  

        $notify[]=['success','Charge updated successfully'];
        return back()->withNotify($notify);
    }

}
