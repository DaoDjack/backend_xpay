@extends('admin.layouts.app')

@section('panel')
<script src="http://www.x-paycash.comx/assets/global/js/jquery-3.6.0.min.js"></script>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="payment-method-item">
                        <div class="payment-method-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border--primary mb-3">
                                        <h5 class="card-header bg--primary">@lang('Money Transfer/Request Charge')</h5>
                                        <div class="card-body">
                                            <form action="{{route('admin.transaction.charges.update')}}" method="post" id="moneytransfer">
                                                @csrf
                                                <input type="hidden" name="id" id="money_id"  class=" money" value="{{$moneyTransfer->id}}">
                                                 <input type="hidden" name="slug" id="money_slug" class="" value="{{$moneyTransfer->slug}}">
                                                <div class="input-group has_append mb-3">
                                                <label class="w-100">@lang('Intervalle Charge') </label> <?php //echo $moneyList?>
                                                <select class="form-control" id="money"  onchange="selection(this);">
                                                    <?php foreach($moneyList as $money)
                                                    {
                                                        echo '<option value="'.$money['value'].'">'.$money['label'].'</option>';
                                                    }
                                                    ?>
                                                <option value="">[Add new transaction charge]</option>
												</select>
                                                
                                                    <div class="input-group-text">{{$moneyTransfer->currency}}</div>
                                                   &nbsp; <button type="button"   class="btn btn--danger   mt-2" onclick="deleteCharges('money');">@lang('Remove')</button>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Minimum Amount') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control money" required="required" name="min_limit"  id="money_min_limit" placeholder="0" value="{{ getAmount($moneyTransfer->min_limit,2) }}"/>
                                                        <div class="input-group-text"> {{$moneyTransfer->currency}} </div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Maximum Amount') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control money" required="required" name="max_limit"  id="money_max_limit" placeholder="0" value="{{  getAmount($moneyTransfer->max_limit,2) }}"/>
                                                        <div class="input-group-text"> {{$moneyTransfer->currency}} </div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                <label class="w-100">@lang('Percentage Charge') <span class="text-danger">*</span></label>
                                                <input type="number" step="any" class="form-control money" name="percent_charge"  id="money_percent_charge" placeholder="0" value="{{ getAmount($moneyTransfer->percent_charge,2) }}"/>
                                                    <div class="input-group-text">%</div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Fixed Charge') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control money" name="fixed_charge"  id="money_fixed_charge"placeholder="0" value="{{ getAmount($moneyTransfer->fixed_charge,2) }}"/>
                                                        <div class="input-group-text"> {{$moneyTransfer->currency}} </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="input-group has_append mb-3">
                                                            <label class="w-100">@lang('Daily Transfer Limit') <span class="text-danger">*</span><code class="text--primary">@lang('(Put -1 if you don\'t want limit)')</code></label>
                                                            <input type="number" step="any" class="form-control" name="daily_limit" id="money_daily_limit" placeholder="0" value="{{  getAmount($moneyTransfer->daily_limit,2) }}"/>
                                                                <div class="input-group-text"> {{$moneyTransfer->currency}} </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="input-group has_append mb-3">
                                                            <label class="w-100">@lang('Daily Request Accept Limit') <span class="text-danger">*</span><code class="text--primary">@lang('(Put -1 for unlimited)')</code></label>
                                                            <input type="number" step="any" class="form-control" name="daily_request_accept_limit" id="money_daily_request_accept_limit" placeholder="0" value="{{  getAmount($moneyTransfer->daily_request_accept_limit,2) }}"/>
                                                                <div class="input-group-text"> {{$moneyTransfer->currency}} </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Maximum Charge Cap') <span class="text-danger">*</span> <code class="text--primary">@lang('(Put -1 if you don\'t want charge cap)')</code></label>
                                                    <input type="number" step="any" class="form-control" name="cap"  id="money_cap" placeholder="0" value="{{  getAmount($moneyTransfer->cap) }}"/>
                                                        <div class="input-group-text"> {{$moneyTransfer->currency}} </div>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn--primary w-100 h-45 mt-2">@lang('Submit')</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border--primary mb-3">
                                        <h5 class="card-header bg--primary">@lang('Voucher Create Charge')</h5>
                                        <div class="card-body">
                                            <form action="{{route('admin.transaction.charges.update')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="id" id="voucher_id" class="voucher" value="{{$voucherCharge->id}}">
                                                <input type="hidden" name="slug" id="voucher_slug" class="" value="{{$voucherCharge->slug}}">
                                                <div class="input-group has_append mb-3">
                                                <label class="w-100">@lang('Intervalle Charge') </label> <?php //echo $moneyList?>
                                                <select class="form-control" id="voucher"  onchange="selection(this);">
                                                    <?php foreach($voucherList as $voucher)
                                                    {
                                                        echo '<option value="'.$voucher['value'].'">'.$voucher['label'].'</option>';
                                                    }
                                                    ?>
                                                <option value="">[Add new transaction charge]</option>
												</select>
                                                
                                                    <div class="input-group-text">{{$voucherCharge->currency}}</div>
                                                   &nbsp; <button type="button"   class="btn btn--danger   mt-2" onclick="deleteCharges('voucher');">@lang('Remove')</button>
                                                </div>
                                                <div class="row">
                                                    <div class="input-group has_append mb-3 col-md-12">
                                                        <label class="w-100">@lang('Minimum Amount') <span class="text-danger">*</span></label>
                                                        <input type="number" step="any" class="form-control voucher" name="min_limit" placeholder="0" id="voucher_min_limit" value="{{ getAmount($voucherCharge->min_limit,2) }}"/>
                                                            <div class="input-group-text"> {{$voucherCharge->currency}} </div>
                                                    </div>
                                                    <div class="input-group has_append mb-3 col-md-12">
                                                        <label class="w-100">@lang('Maximum Amount') <span class="text-danger">*</span></label>
                                                        <input type="number" step="any" class="form-control voucher" name="max_limit" id="voucher_max_limit" placeholder="0" value="{{  getAmount($voucherCharge->max_limit,2) }}"/>
                                                            <div class="input-group-text"> {{$voucherCharge->currency}} </div>
                                                    </div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                <label class="w-100">@lang('Percentage Charge') <span class="text-danger">*</span></label>
                                                <input type="number" step="any" class="form-control voucher" name="percent_charge"  id="voucher_percent_charge" placeholder="0" value="{{ getAmount($voucherCharge->percent_charge,2) }}"/>
                                                    <div class="input-group-text">%</div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Fixed Charge') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control voucher" name="fixed_charge"  id="voucher_fixed_charge" placeholder="0" value="{{ getAmount($voucherCharge->fixed_charge,2) }}"/>
                                                        <div class="input-group-text"> {{$voucherCharge->currency}} </div>
                                                </div>
                                                
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Daily Voucher Create Limit') <span class="text-danger">*</span> <code class="text--primary">@lang('(Put -1 if you don\'t want limit)')</code> </label>
                                                    <input type="number" step="any" class="form-control" name="voucher_limit"  id="voucher_voucher_limit" placeholder="0" value="{{  getAmount($voucherCharge->voucher_limit) }}"/>
                                                    
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Maximum Charge Cap') <span class="text-danger">*</span> <code class="text--primary">@lang('(Put -1 if you don\'t want charge cap)')</code> </label>
                                                    <input type="number" step="any" class="form-control" name="cap"  id="voucher_cap" placeholder="0" value="{{  getAmount($voucherCharge->cap,2) }}"/>
                                                        <div class="input-group-text"> {{$voucherCharge->currency}} </div>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn--primary w-100 h-45 mt-2">@lang('Submit')</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border--primary mb-3">
                                        <h5 class="card-header bg--primary">@lang('Invoice Charge')</h5>
                                        <div class="card-body">
                                            <form action="{{route('admin.transaction.charges.update')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="id" id="invoice_id" class="invoice" value="{{$invoiceCharge->id}}">
                                                <input type="hidden" name="slug" id="invoice_slug" class="" value="{{$invoiceCharge->slug}}">
                                                <div class="input-group has_append mb-3" style="display:none">
                                                <label class="w-100">@lang('Intervalle Charge') </label> <?php //echo $moneyList?>
                                                <select class="form-control" id="invoice"  onchange="selection(this);">
                                                    <?php foreach($invoiceList as $voucher)
                                                    {
                                                        echo '<option value="'.$voucher['value'].'">'.$voucher['label'].'</option>';
                                                    }
                                                    ?>
                                                <option value="">[Add new transaction charge]</option>
												</select>
                                                
                                                    <div class="input-group-text">{{$invoiceCharge->currency}}</div>
                                                   &nbsp; <button type="button"   class="btn btn--danger   mt-2" onclick="deleteCharges('money');">@lang('Remove')</button>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Percentage Charge') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control invoice" name="percent_charge" id="invoice_percent_charge" placeholder="0" value="{{ getAmount($invoiceCharge->percent_charge,2) }}"/>
                                                        <div class="input-group-text">%</div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Fixed Charge') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control invoice" name="fixed_charge"  id="invoice_fixed_charge"placeholder="0" value="{{ getAmount($invoiceCharge->fixed_charge,2) }}"/>
                                                        <div class="input-group-text"> {{$invoiceCharge->currency}} </div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Maximum Charge Cap') <span class="text-danger">*</span> <code class="text--primary">@lang('(Put -1 if you don\'t want charge cap)')</code> </label>
                                                    <input type="number" step="any" class="form-control invoice" name="cap"  id="invoice_cap"placeholder="0" value="{{  getAmount($invoiceCharge->cap,2) }}"/>
                                                        <div class="input-group-text"> {{$invoiceCharge->currency}} </div>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn--primary w-100 h-45 mt-2">@lang('Submit')</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border--primary mb-3">
                                        <h5 class="card-header bg--primary">@lang('Money Exchange Charge')</h5>
                                        <div class="card-body">
                                            <form action="{{route('admin.transaction.charges.update')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="id" id="exchange_id" class="exchange" value="{{$exchangeCharge->id}}">
                                                 <input type="hidden" name="slug" id="exchange_slug" class="" value="{{$exchangeCharge->slug}}">
                                                <div class="input-group has_append mb-3" style="display:none">
                                                <label class="w-100">@lang('Intervalle Charge') </label> 
                                                <select class="form-control" id="exchange"  onchange="selection(this);">
                                                    <?php foreach($exchangeList as $exchange)
                                                    {
                                                        echo '<option value="'.$exchange['value'].'">'.$exchange['label'].'</option>';
                                                    }
                                                    ?>
                                                <option value="">[Add new transaction charge]</option>
												</select>
                                                
                                                    <div class="input-group-text">{{$exchangeCharge->currency}}</div>
                                                   &nbsp; <button type="button"   class="btn btn--danger   mt-2" onclick="deleteCharges('exchange');">@lang('Remove')</button>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Percentage Charge') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control exchange" name="percent_charge" id="exchange_percent_charge" placeholder="0" value="{{ getAmount($exchangeCharge->percent_charge,2) }}"/>
                                                        <div class="input-group-text">%</div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Fixed Charge') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control exchange" name="fixed_charge"  id="exchange_fixed_charge" placeholder="0" value="{{ getAmount($exchangeCharge->fixed_charge,2) }}"/>
                                                        <div class="input-group-text"> {{$exchangeCharge->currency}} </div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Maximum Charge Cap') <span class="text-danger">*</span> <code class="text--primary">@lang('(Put -1 if you don\'t want charge cap)')</code> </label>
                                                    <input type="number" step="any" class="form-control exchange" name="cap"  id="exchange_cap" placeholder="0" value="{{  getAmount($exchangeCharge->cap,2) }}"/>
                                                        <div class="input-group-text"> {{$exchangeCharge->currency}} </div>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn--primary w-100 h-45 mt-2">@lang('Submit')</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border--primary mb-3">
                                        <h5 class="card-header bg--primary">@lang('Money Out Charges')</h5>
                                        <div class="card-body">
                                            <form action="{{route('admin.transaction.charges.update')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="id" id="moneyOut_id" class="moneyOut" value="{{$moneyOutCharge->id}}">
                                                 <input type="hidden" name="slug" id="moneyOut_slug" class="" value="{{$moneyOutCharge->slug}}">
                                                <div class="input-group has_append mb-3">
                                                <label class="w-100">@lang('Intervalle Charge') </label> <?php //echo $moneyList?>
                                                <select class="form-control" id="moneyOut"  onchange="selection(this);">
                                                    <?php foreach($moneyOutList as $moneyOut)
                                                    {
                                                        echo '<option value="'.$moneyOut['value'].'">'.$moneyOut['label'].'</option>';
                                                    }
                                                    ?>
                                                <option value="">[Add new transaction charge]</option>
												</select>
                                                
                                                    <div class="input-group-text">{{$moneyOutCharge->currency}}</div>
                                                   &nbsp; <button type="button"   class="btn btn--danger   mt-2" onclick="deleteCharges('moneyOut');">@lang('Remove')</button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="input-group has_append mb-3">
                                                            <label class="w-100">@lang('Minimum Amount') <span class="text-danger">*</span></label>
                                                            <input type="number" step="any" class="form-control moneyOut" name="min_limit"  id="moneyOut_min_limit"placeholder="0" value="{{ getAmount($moneyOutCharge->min_limit,2) }}"/>
                                                                <div class="input-group-text"> {{$moneyOutCharge->currency}} </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-group has_append mb-3">
                                                            <label class="w-100">@lang('Maximum Amount') <span class="text-danger">*</span></label>
                                                            <input type="number" step="any" class="form-control" name="max_limit"  id="moneyOut_max_limit"placeholder="0" value="{{  getAmount($moneyOutCharge->max_limit,2) }}"/>
                                                                <div class="input-group-text"> {{$moneyOutCharge->currency}} </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="input-group has_append mb-3">
                                                            <label class="w-100">@lang('Percentage Charge') <span class="text-danger">*</span></label>
                                                            <input type="number" step="any" class="form-control moneyOut" name="percent_charge"  id="moneyOut_percent_charge"placeholder="0" value="{{ getAmount($moneyOutCharge->percent_charge,2) }}"/>
                                                                <div class="input-group-text">%</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-group has_append mb-3">
                                                            <label class="w-100">@lang('Fixed Charge') <span class="text-danger">*</span></label>
                                                            <input type="number" step="any" class="form-control moneyOut" name="fixed_charge"  id="moneyOut_fixed_charge"placeholder="0" value="{{ getAmount($moneyOutCharge->fixed_charge,2) }}"/>
                                                                <div class="input-group-text"> {{$moneyOutCharge->currency}} </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Agent Commission (fixed)') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control moneyOut" name="agent_commission_fixed"  id="moneyOut_agent_commission_fixed"placeholder="0" value="{{  getAmount($moneyOutCharge->agent_commission_fixed,2) }}"/>
                                                        <div class="input-group-text"> {{$moneyOutCharge->currency}} </div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Agent Commission (%)') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control moneyOut" name="agent_commission_percent"  id="moneyOut_agent_commission_percent"placeholder="0" value="{{  getAmount($moneyOutCharge->agent_commission_percent,2) }}"/>
                                                        <div class="input-group-text">%</div>
                                                </div>

                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Daily Money Out Limit') <span class="text-danger">*</span> <code class="text--primary">@lang('(Put -1 if you don\'t want limit)')</code> </label>
                                                    <input type="number" step="any" class="form-control moneyOut" name="daily_limit"  id="moneyOut_daily_limit"placeholder="0" value="{{  getAmount($moneyOutCharge->daily_limit,2) }}"/>
                                                        <div class="input-group-text"> {{$moneyOutCharge->currency}} </div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Monthly Money Out Limit') <span class="text-danger">*</span><code class="text--primary">@lang('(Put -1 if you don\'t want limit)')</code> </label>
                                                    <input type="number" step="any" class="form-control moneyOut" name="monthly_limit" id="moneyOut_monthly_limit" placeholder="0" value="{{  getAmount($moneyOutCharge->monthly_limit,2) }}"/>
                                                        <div class="input-group-text"> {{$moneyOutCharge->currency}} </div>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn--primary w-100 h-45 mt-2">@lang('Submit')</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border--primary mb-3">
                                        <h5 class="card-header bg--primary">@lang('Money In Charges')</h5>
                                        <div class="card-body">
                                            <form action="{{route('admin.transaction.charges.update')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="id" id="moneyIn_id" class="moneyIn" value="{{$moneyInCharge->id}}">
                                                 <input type="hidden" name="slug" id="moneyIn_slug" class="" value="{{$moneyInCharge->slug}}">
                                                <div class="input-group has_append mb-3">
                                                <label class="w-100">@lang('Intervalle Charge') </label> <?php //echo $moneyList?>
                                                <select class="form-control" id="moneyIn"  onchange="selection(this);">
                                                    <?php foreach($moneyInList as $voucher)
                                                    {
                                                        echo '<option value="'.$voucher['value'].'">'.$voucher['label'].'</option>';
                                                    }
                                                    ?>
                                                <option value="">[Add new transaction charge]</option>
												</select>
                                                
                                                    <div class="input-group-text">{{$moneyInCharge->currency}}</div>
                                                   &nbsp; <button type="button"   class="btn btn--danger   mt-2" onclick="deleteCharges('moneyIn');">@lang('Remove')</button>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Minimum Amount') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control moneyIn" name="min_limit"  id="moneyIn_min_limit" placeholder="0" value="{{ getAmount($moneyInCharge->min_limit,2) }}"/>
                                                        <div class="input-group-text"> {{$moneyInCharge->currency}} </div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Maximum Amount') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control moneyIn" name="max_limit" id="moneyIn_max_limit" placeholder="0" value="{{  getAmount($moneyInCharge->max_limit,2) }}"/>
                                                        <div class="input-group-text"> {{$moneyInCharge->currency}} </div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Agent Commission (fixed)') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control moneyIn" name="agent_commission_fixed" id="moneyIn_agent_commission_fixed" placeholder="0" value="{{  getAmount($moneyInCharge->agent_commission_fixed,2) }}"/>
                                                        <div class="input-group-text"> {{$moneyInCharge->currency}} </div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Agent Commission (%)') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control moneyIn" name="agent_commission_percent" id="moneyIn_agent_commission_percent" placeholder="0" value="{{  getAmount($moneyInCharge->agent_commission_percent,2) }}"/>
                                                        <div class="input-group-text">%</div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Daily Money In Limit') <span class="text-danger">*</span><code class="text--primary">@lang('(Put -1 if you don\'t want limit)')</code></label>
                                                    <input type="number" step="any" class="form-control moneyIn" name="daily_limit" id="moneyIn_daily_limit" placeholder="0" value="{{  getAmount($moneyInCharge->daily_limit,2) }}"/>
                                                        <div class="input-group-text"> {{$moneyInCharge->currency}} </div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Monthly Money In Limit') <span class="text-danger">*</span><code class="text--primary">@lang('(Put -1 if you don\'t want limit)')</code></label>
                                                    <input type="number" step="any" class="form-control moneyIn" name="monthly_limit" id="moneyIn_monthly_limit" placeholder="0" value="{{  getAmount($moneyInCharge->monthly_limit,2) }}"/>
                                                        <div class="input-group-text"> {{$moneyInCharge->currency}} </div>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn--primary w-100 h-45 mt-2">@lang('Submit')</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border--primary">
                                        <h5 class="card-header bg--primary">@lang('Api Payment Charge')</h5>
                                        <div class="card-body">
                                            <form action="{{route('admin.transaction.charges.update')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="id" id="api_id" class="api" value="{{$apiCharge->id}}">
                                                 <input type="hidden" name="slug" id="api_slug" class="" value="{{$apiCharge->slug}}">
                                                <div class="input-group has_append mb-3" style="display:none">
                                                <label class="w-100">@lang('Intervalle Charge') </label> <?php //echo $moneyList?>
                                                <select class="form-control" id="api"  onchange="selection(this);">
                                                    <?php foreach($apiList as $voucher)
                                                    {
                                                        echo '<option value="'.$voucher['value'].'">'.$voucher['label'].'</option>';
                                                    }
                                                    ?>
                                                <option value="">[Add new transaction charge]</option>
												</select>
                                                
                                                    <div class="input-group-text">{{$apiCharge->currency}}</div>
                                                   &nbsp; <button type="button"   class="btn btn--danger   mt-2" onclick="deleteCharges('api');">@lang('Remove')</button>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Percentage Charge') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control" name="percent_charge"  id="api_id"placeholder="0" value="{{ getAmount($apiCharge->percent_charge,2) }}"/>
                                                        <div class="input-group-text">%</div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Fixed Charge') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control" name="fixed_charge"  id="api_id"placeholder="0" value="{{ getAmount($apiCharge->fixed_charge,2) }}"/>
                                                        <div class="input-group-text"> {{$apiCharge->currency}} </div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Maximum Charge Cap') <span class="text-danger">*</span> <code class="text--primary">@lang('(Put -1 if you don\'t want charge cap)')</code> </label>
                                                    <input type="number" step="any" class="form-control" name="cap"  id="api_id"placeholder="0" value="{{  getAmount($apiCharge->cap,2) }}"/>
                                                        <div class="input-group-text"> {{$apiCharge->currency}} </div>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn--primary w-100 h-45 mt-2">@lang('Submit')</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border--primary">
                                        <h5 class="card-header bg--primary">@lang('Make Payment Charges')</h5>
                                        <div class="card-body">
                                            <form action="{{route('admin.transaction.charges.update')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="id" id="payment_id" class="payment" value="{{$paymentCharge->id}}">
                                                 <input type="hidden" name="slug" id="payment_slug" class="" value="{{$paymentCharge->slug}}">
                                                <div class="input-group has_append mb-3" style="display:none">
                                                <label class="w-100">@lang('Intervalle Charge') </label> <?php //echo $moneyList?>
                                                <select class="form-control" id="payment"  onchange="selection(this);">
                                                    <?php foreach($paymentList as $payment)
                                                    {
                                                        echo '<option value="'.$payment['value'].'">'.$payment['label'].'</option>';
                                                    }
                                                    ?>
                                                <option value="">[Add new transaction charge]</option>
												</select>
                                                
                                                    <div class="input-group-text">{{$paymentCharge->currency}}</div>
                                                   &nbsp; <button type="button"   class="btn btn--danger   mt-2" onclick="deleteCharges('payment');">@lang('Remove')</button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="input-group has_append mb-3">
                                                            <label class="w-100">@lang('User Percentage Charge') <span class="text-danger">*</span></label>
                                                            <input type="number" step="any" class="form-control payment" name="percent_charge"  id="payment_percent_charge"placeholder="0" value="{{ getAmount($paymentCharge->percent_charge,2) }}"/>
                                                                <div class="input-group-text">%</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-group has_append mb-3">
                                                            <label class="w-100">@lang('User Fixed Charge') <span class="text-danger">*</span></label>
                                                            <input type="number" step="any" class="form-control payment" name="fixed_charge"  id="payment_fixed_charge"placeholder="0" value="{{ getAmount($paymentCharge->fixed_charge,2) }}"/>
                                                                <div class="input-group-text"> {{$paymentCharge->currency}} </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Merchant percent charge') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control payment" name="merchant_percent_charge"  id="payment_merchant_percent_charge" placeholder="0" value="{{  getAmount($paymentCharge->merchant_percent_charge,2) }}"/>
                                                        <div class="input-group-text">%</div>
                                                </div>
                                                <div class="input-group has_append mb-3">
                                                    <label class="w-100">@lang('Merchant fixed charge') <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" class="form-control payment" name="merchant_fixed_charge"  id="payment_merchant_fixed_charge"placeholder="0" value="{{  getAmount($paymentCharge->merchant_fixed_charge,2) }}"/>
                                                        <div class="input-group-text"> {{$paymentCharge->currency}} </div>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn--primary w-100 h-45 mt-2">@lang('Submit')</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- card end -->
        </div>
    </div>
      <script>
	  function selection(code)
	  {
		 
		  var value = $.trim(code.value);
		  var vid = $.trim(code.id);
		  if(value=='')
			{
				var myclass = "."+vid;

				$(myclass).each(function( index ) {
				  $( this ).val(0);
				});
			}
			else
			{
				//Function ajax to get now data	
				url = "{{route('admin.transaction.charges.detail')}}?id="+value;
	
				
		$.ajax({
			   type: "GET",
			   url: url,
			   success: function(msg)
					   {
							
							if(msg.error==0)
							{
								var data = msg.data;
								for(var key in data)
								{
									var id = vid+'_'+key;
									
									if($('#'+id).val()!=undefined)
									{

										$('#'+id).val(data[key]);	
									}
								}
								
								
							}
							else alert(msg.message);
					   }
			 });
			}
	  }
	  function deleteCharges(code)
	  {
		  var oui = confirm("@lang('Remove intervalle charge')");
		  if(oui)
		  {
			  var val = $('#'+code+'_id').val();
			  url = "{{route('admin.transaction.charges.delete')}}?id="+val;

			  $.ajax({
			   type: "GET",
			   url: url,
			   success: function(msg)
					   {
							self.location = "{{route('admin.transaction.charges')}}";
					   }
			 });
			
		  }
		  else return false;
	  }
	  function detailCharges(code)
	  {
		  //alert(code);
	  }
        $(document).ready(function(){
	
	  $('.moneychange').change
	  (
		function ()
		{
			var value = $.trim(this.value);
			
			
		}
	  );

});
    </script>
@endsection


