<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCharge extends Model
{
    use HasFactory;
    protected $fillable = ['slug','fixed_charge','percent_charge','min_limit','max_limit','monthly_limit','daily_limit','voucher_limit','agent_commission_fixed','agent_commission_percent','merchant_fixed_charge','merchant_percent_charge'];
    /*
    'fixed_charge' => $_REQUEST['fixed_charge'],
            'cap'   => $_REQUEST['cap'],
            'min_limit' => $_REQUEST['min_limit'],
            'max_limit' => $_REQUEST['max_limit'],
            'monthly_limit' => $_REQUEST['monthly_limit'],
            'daily_limit' => $_REQUEST['daily_limit'],
            'voucher_limit' => $_REQUEST['voucher_limit'],
            'agent_commission_fixed' =>$_REQUEST['agent_commission_fixed'],
            'agent_commission_percent' => $_REQUEST['agent_commission_percent'],
            'merchant_fixed_charge' => $_REQUEST['merchant_fixed_charge'],
        'merchant_percent_charge' => $_REQUEST['merchant_percent_charge']];
    */
}
