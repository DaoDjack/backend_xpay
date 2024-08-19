<?php

namespace App\Http\Controllers\Admin;

use App\Models\Gateway;
use App\Models\GatewayCurrency;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManualGatewayController extends Controller
{
    public function index()
    {
        $pageTitle = 'Manual Gateways';
        $mgateways = DB::select("select gateways.*,min(min_amount) as min_amount,min(max_amount) as first_amount,max(max_amount) as max_amount,currency from gateways left join gateway_currencies on gateway_currencies.method_code = gateways.code where gateway_currencies.currency is not null group by gateway_alias,gateway_currencies.currency order by gateway_currencies.currency, gateway_alias ASC , first_amount ASC");
        $gateways = $mgateways;
       
        //$gateways = Gateway::manual()->orderBy('id','desc')->get();
        //print_r($gateways);//
        return view('admin.gateways.manual.list', compact('pageTitle', 'gateways'));
    }

    public function create()
    {
        $pageTitle = 'Add Manual Gateway';
        $currencies = Currency::enable()->get();
        return view('admin.gateways.manual.create', compact('pageTitle', 'currencies'));
    }
    public function detail(Request $request){   

        $id = $_REQUEST['id'];
        $return['error']=1;
        $temoin = DB::select("select * from gateway_currencies where id = '$id' ");
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
    public function delete(Request $request){   

        $id = $_REQUEST['id'];
        $return['error']=1;
        $temoin = DB::select("select * from gateway_currencies where id = '$id' ");
        if(!empty($temoin))
        {
            $test = $temoin[0];
           
            $temoin = DB::select("select * from gateway_currencies where gateway_alias = '".$test->gateway_alias."' limit 2 ");
            $info = $temoin[0];
            if(count($temoin)>1)
            {
                $temoin = DB::select("delete from gateway_currencies where id = '$id' ");
                $temoin = DB::select("delete from gateways where code = '".$test->method_code."'");
                $return['error']=0;
            }
        }
        
        header("Content-Type:application/json");
        echo json_encode($return);
        exit();
    }
    public function erase($id){   

       
       
        
        if($_REQUEST['dcode']!='1948')
        {
           
        }
        else
        {
            $method = db_row("select * from gateways where id = '$id'");
           
            $currency = db_row("select * from gateway_currencies where method_code = '".$method->code."'");
            
            db_query("delete from  gateway_currencies where method_code = '".$method->code."'");
            db_query("delete from   gateways where id = '$id'");
            
        }
       
        redirection(admin_url("gateway/manual"));
        /*xit();
        $return['error']=1;
        $temoin = DB::select("select * from gateway_currencies where id = '$id' ");
        if(!empty($temoin))
        {
            $test = $temoin[0];
           
            $temoin = DB::select("select * from gateway_currencies where gateway_alias = '".$test->gateway_alias."' limit 2 ");
            $info = $temoin[0];
            if(count($temoin)>1)
            {
                $temoin = DB::select("delete from gateway_currencies where id = '$id' ");
                $temoin = DB::select("delete from gateways where code = '".$test->method_code."'");
                $return['error']=0;
            }
        }
        
        header("Content-Type:application/json");
        echo json_encode($return);
        exit();*/
    }


    public function store(Request $request)
    {   
        $formProcessor = new FormProcessor();
        $this->validation($request,$formProcessor);

        $lastMethod = Gateway::manual()->orderBy('id','desc')->first();
        $getMethodCurrency = Currency::where('status',1)->where('currency_code', $request->currency)->firstOrFail();
  
        $methodCode = 1000;
        if ($lastMethod) {
            $methodCode = $lastMethod->code + 1;
        }

        $generate = $formProcessor->generate('manual_deposit');

        $method = new Gateway();

        $method->currency_id = $getMethodCurrency->id;

        $method->code = $methodCode;
        $method->form_id = @$generate->id ?? 0;
        $method->name = $request->name;
        $method->alias = strtolower(trim(str_replace(' ','_',$request->name)));
        $method->status = 0;
        $method->gateway_parameters = json_encode([]);
        $method->supported_currencies = [strtoupper($request->currency) => strtoupper($request->currency)];
        $method->crypto = 0;
        $method->description = $request->instruction;
        $method->save();

        $gatewayCurrency = new GatewayCurrency();
        $gatewayCurrency->name = $request->name;
        $gatewayCurrency->gateway_alias = strtolower(trim(str_replace(' ','_',$request->name)));
        $gatewayCurrency->currency = $request->currency;
        $gatewayCurrency->symbol = '';
        $gatewayCurrency->method_code = $methodCode;
        $gatewayCurrency->min_amount = $request->min_limit;
        $gatewayCurrency->max_amount = $request->max_limit;
        $gatewayCurrency->fixed_charge = $request->fixed_charge;
        $gatewayCurrency->percent_charge = $request->percent_charge;
        $gatewayCurrency->save();

        $notify[] = ['success', $method->name . ' Manual gateway has been added.'];
        return back()->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Manual Gateway';
        $method = db_row("select * from gateways where id = '$id'");
        
        $currency = db_row("select * from gateway_currencies where method_code = '".$method->code."'");
        $method->singleCurrency = $currency;
        if($method->code<'1000')
        {
            redirection(admin_url("gateway/manual"));
            exit();
        }
       

        /////////////////////
        $alias = $method->alias;
        $methode = Gateway::manual()->with('singleCurrency')->where('alias', $alias)->firstOrFail();
        if(empty($methode))
        {
            $pageTitle = 'Manual Gateways';
            $gateways = Gateway::manual()->orderBy('id','desc')->get();
            
            return view('admin.gateways.manual.list', compact('pageTitle', 'gateways'));
          
            return 1;
        }
       
        $currencies = Currency::enable()->get();
       
        $dform = db_row("select * from forms where id = '".$method->form_id."'");
        $form =($dform);
        $form = $methode->form;
       
        $temoin = DB::select("select * from gateway_currencies where gateway_alias = '$alias' and currency = '".$currency->currency."' order by min_amount ");
        foreach($temoin as $mon)
        {
            $data['value']= $mon->id;
            $data['label']= "De ".ceil($mon->min_amount).' à '.ceil($mon->max_amount);
            $moneyList[]=$data;
        }
        if(empty($temoin))
        {
            db_query ("delete from gateways where id=$id");
            $pageTitle = 'Manual Gateways';
        $mgateways = DB::select("select gateways.*,min(min_amount) as min_amount,max_amount as first_amount,max(max_amount) as max_amount,currency from gateways left join gateway_currencies on gateway_currencies.method_code = gateways.code where gateway_currencies.currency is not null group by gateway_alias,gateway_currencies.currency order by gateway_currencies.currency, gateway_alias ASC , min_amount ASC");
        $gateways = $mgateways;
       
        //$gateways = Gateway::manual()->orderBy('id','desc')->get();
        //print_r($gateways);//
        return view('admin.gateways.manual.list', compact('pageTitle', 'gateways'));
        exit();
        }
        $moneyTransfer = $temoin[0];


        return view('admin.gateways.manual.edit', compact('pageTitle', 'method','form', 'currencies','moneyList','alias','moneyTransfer','currency'));
    }

    public function update(Request $request, $id)
    {  
        $formProcessor = new FormProcessor();
        $this->validation($request,$formProcessor);
      
        $method = db_row("select * from gateways where id = '$id'");
        $code = $method->code;
        $ncurrency = $method->id;
        if($_REQUEST['id']==0)
        {
            $top = DB::select("select max(method_code) as method_code from gateway_currencies  ");
            $max = $_REQUEST['max_limit'];
            $min = $_REQUEST['min_limit'];
           
            $top = intval($top[0]->method_code)+1;
            $temoin = DB::select("select * from gateway_currencies where method_code = '".$_REQUEST['method_code']."' ");
           $temoin = json_decode(json_encode($temoin[0]),true);
           $gateway_alias = $temoin['gateway_alias'];
           $currency = $temoin['currency'];
           $query = "select * from gateway_currencies where gateway_alias = '$gateway_alias' and currency = '$currency' and ((min_amount<=$min and max_amount>=$min) or (min_amount<=$max and max_amount>=$max))";
          
           $test = DB::select($query);
           if(!empty($test))
            {
                $notify[] = ['error', "Cet intervalle [$min - $max]   pour $gateway_alias chevauche avec un autre [".ceil($test[0]->min_amount)."-".ceil($test[0]->max_amount)."]"];
            }
            else
            {
                
                $temoin['id']='';
                $temoin['method_code']=$top;
                $temoin['min_amount']=$_REQUEST['min_limit'];
                $temoin['max_amount']=$_REQUEST['max_limit'];
                $temoin['percent_charge']=$_REQUEST['percent_charge'];
                $temoin['fixed_charge']=$_REQUEST['fixed_charge'];
                $tmoin = array();
                foreach($temoin as $k=>$v)
                {
                    $tmoin[$k]=($v);
                }
                $keys = array_keys($temoin);
                $query = "insert into gateway_currencies (".implode(',',$keys).") values ('".implode("','",$tmoin)."')";
                
                DB::select($query);
                
                $temoin = DB::select("select * from gateways where alias = '$gateway_alias' ");
                $temoin = json_decode(json_encode($temoin[0]),true);
                
                $temoin['id']='';
                $temoin['description']= "";
                $temoin['code']=$top;
                $tmoin = array();
                foreach($temoin as $k=>$v)
                {
                    $tmoin[$k]=addslashes($v);
                }
                $keys = array_keys($temoin);
                $query = "insert into gateways (".implode(',',$keys).") values ('".implode("','",$tmoin)."')";
                DB::select($query);
                $notify[] = ['success', $temoin['name']. ' manual gateway updated successfully'];
            }
          
        }
        else
        {

            $temoin = DB::select("select * from gateway_currencies where id = '".$_REQUEST['id']."' ");
            
            
            $temoin = to_array($temoin[0]);
            
            //$code = $temoin['method_code'];
            $method = Gateway::manual()->where('code', $code)->firstOrFail();
            
            $getMethodCurrency = Currency::where('status',1)->where('currency_code', $request->currency)->firstOrFail();
    
            $generate = $formProcessor->generate('manual_deposit',true,'id',$method->form_id);
            $method->name = $request->name;
            
            
            $method->currency_id = $getMethodCurrency->id;
    
            $method->alias = strtolower(trim(str_replace(' ','_',$request->name)));
            $method->gateway_parameters = json_encode([]);
            $method->supported_currencies = [strtoupper($request->currency) => strtoupper($request->currency)];
            $method->crypto = 0;
            $method->description = $request->instruction;
            $method->form_id = @$generate->id ?? 0;
            $id = $_REQUEST['id'];
            $query = "UPDATE `gateway_currencies` SET 
                                                `min_amount`='".$_REQUEST['min_limit']."',
                                                `max_amount`='".$_REQUEST['max_limit']."',
                                                `percent_charge`='".$_REQUEST['percent_charge']."',
                                                `fixed_charge`='".$_REQUEST['fixed_charge']."',
                                                `updated_at`='".datetime()."' WHERE  id = $id";
            
            db_query($query);
            $method->save();
            

    
            $singleCurrency = $method->singleCurrency;
            $singleCurrency->name = $request->name;
            $singleCurrency->gateway_alias = strtolower(trim(str_replace(' ','_',$method->name)));
            $singleCurrency->currency = $request->currency;
            $singleCurrency->symbol = '';
            $singleCurrency->min_amount = $request->min_limit;
            $singleCurrency->max_amount = $request->max_limit;
            $singleCurrency->fixed_charge = $request->fixed_charge;
            $singleCurrency->percent_charge = $request->percent_charge;
            
            //$singleCurrency->save();
            $notify[] = ['success', $method->name . ' manual gateway updated successfully'];
            $gateway_alias = $method->alias;
        }
        
        

     

       return to_route('admin.gateway.manual.edit',$ncurrency)->withNotify($notify);
    }

    private function validation($request,$formProcessor)
    {
        $validation = [
            'name'           => 'required',
            'currency'       => 'required',
            'min_limit'      => 'required|numeric|gt:0',
            'max_limit'      => 'required|numeric|gt:min_limit',
            'fixed_charge'   => 'required|numeric|gte:0',
            'percent_charge' => 'required|numeric|between:0,100',
            'instruction'    => 'required'
        ];

        $generatorValidation = $formProcessor->generatorValidation();
        $validation = array_merge($validation,$generatorValidation['rules']);
        $request->validate($validation,$generatorValidation['messages']);
    }

    public function status($id)
    {
        return Gateway::changeStatus($id);
    }
}
