@extends($activeTemplate . 'layouts.' . strtolower(userGuard()['type']) . '_master')

@php
    $class = '';
    if (userGuard()['type'] == 'AGENT' || userGuard()['type'] == 'MERCHANT') {
        $class = 'mt-5';
    }
@endphp

@section('content')
<?php  //print_r($gateways);?>
    <form action="{{ route(strtolower(userGuard()['type']) . '.deposit.insert') }}" method="POST" id="form">
        @csrf
        <div class="row justify-content-center gy-4 {{ $class }}">
            <div class="col-lg-6">
                <div class="add-money-card">
                    <h4 class="title"><i class="las la-plus-circle"></i> @lang('Add MoneyuserGuard')</h4>
                    <div class="form-group">
                        <label>@lang('Select Your Wallet')</label>
                        <input type="hidden" name="currency">
                        <input type="hidden" name="currency_id">
                        <select class="select" name="wallet_id" id="wallet" required>
                            <option>@lang('Select Wallet')</option>
                            @foreach (userGuard()['user']->wallets as $wallet)
                            
                                <option value="{{ $wallet->id }}" data-code="{{ $wallet->currency->currency_code }}"
                                    data-sym="{{ $wallet->currency->currency_symbol }}"
                                    data-rate="{{ $wallet->currency->rate }}"
                                    data-currency="{{ $wallet->currency->id }}"
                                    data-type="{{ $wallet->currency->currency_type }}"
                                    data-gateways="{{ $wallet->gateways() }}"
                                >
                                    @lang($wallet->currency->currency_code)
                                </option>
                            @endforeach
                        </select> <option>@lang('Select Wallet')</option>
                            @foreach (userGuard()['user']->wallets as $wallet)
                                <!--<textarea value="{{ $wallet->id }}" cols="20"
                                >{{ $wallet }}
                                -----------------------------------------
                               
                                   <?php  print_r($wallet);if(isset($gateways[$wallet->currency->gateway_alias])) echo json_encode($gateways[$wallet->currency->currency_code]);//['USD']; 
								   else json_encode(array());
								   ?>
                                   -->
                                </textarea>
                            @endforeach
                    </div>
                    <div class="form-group">
                        <label>@lang('Select Gateway')</label>
                        <select class="select gateway" name="method_code" disabled required>
                            <option value="">@lang('Select Gateway')</option>
                        </select>
                        <code class="text--danger gateway-msg"></code>
                    </div>
                    <div class="form-group mb-0">
                        <label>@lang('Amount')</label>
                        <div class="input-group">
                            <input class="form--control amount" type="number" step="any" name="amount" disabled
                                placeholder="Enter Amount" required>
                            <span class="input-group-text curr_code"></span>
                        </div>
                        <p><code class="text--warning limit">@lang('limit') : 0.00 <span class="curr_code"></span></code>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="add-money-card style--two">
                    <h4 class="title"><i class="lar la-file-alt"></i> @lang('Summery')</h4>
                    <div class="add-moeny-card-middle">
                        <ul class="add-money-details-list">
                            <li>
                                <span class="caption">@lang('Amount')</span>
                                <div class="value">
                                    <span class="sym">{{ $general->cur_sym }}</span><span class="show-amount">0.00</span>
                                </div>
                            </li>
                            <li>
                                <span class="caption">@lang('Charge')</span>
                                <div class="value"> 
                                    <span class="sym">{{ $general->cur_sym }}</span><span class="charge">0.00</span> 
                                </div>
                            </li>
                        </ul>
                        <div class="add-money-details-bottom">
                            <span class="caption">@lang('Payable')</span>
                            <div class="value">
                                <span class="sym">{{ $general->cur_sym }}</span><span class="payable">0.00</span> 
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-md btn--base w-100 mt-3 req_confirm">@lang('Proceed')</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('script')
    <script>
	
        'use strict';
		function in_array(needle, haystack) {//verifie si un element est dans le tableau.
			for(var i in haystack) {
				if(haystack[i] == needle) return true;
			}
			return false;
		}
		function unique_gateways(gateways) //affiche les gateways sans les doublons
		{
			var ugateways = Array();//Gateway unique
			var ngateways = Array();//Nom deja enregistré
			for(var i in gateways)
			{
				if(!in_array(gateways[i].name,ngateways))
				{
					ugateways.push(gateways[i]);	
					ngateways.push(gateways[i].name);
				}
			}
			return ugateways;
		}
		function facturation (amount,gateway,gateways)//calcule la facturation en fonction du montant et de la gateway
		{
			var tarification = {fixed_charge:0,percent_charge:-1,min_amount:0,max_amount:0,currency:''};
			var trouve = false;
			var type = 1;
			var min_amount,max_amount;
			for(var i in gateways)
			{

				if(gateways[i].gateway_alias==gateway)//si on a choisi la gateway
				{
					min_amount = parseFloat(gateways[i].min_amount);
					max_amount = parseFloat(gateways[i].max_amount);
					
					if(min_amount<=amount && max_amount>=amount )
					{
						
						tarification  = gateways[i];
						break;
					}
				}
			}	
			
			if(tarification.type) type = tarification.type;
			if(tarification.currency=='') amount = 0;//Cas ou aucune tarification n'a été appliqué
			var fixed = parseFloat(tarification.fixed_charge)
			var percent = amount * parseFloat(tarification.percent_charge)/100;
			var totalCharge = fixed + percent
			
			var totalAmount = amount + totalCharge
			var precesion = 0;
			window['totalamount'] = totalAmount;
			if (type == 1) {
				precesion = 2;
			} else {
				precesion = 8;
			}
			var min = parseFloat(tarification.min_amount);
			var max = parseFloat(tarification.max_amount);

			if (!isNaN(amount)) {
				$('.show-amount').text(amount.toFixed(precesion))
				$('.charge').text(totalCharge.toFixed(precesion))
				$('.payable').text(totalAmount.toFixed(precesion))
			} else {
				$('.show-amount').text('0.00')
				$('.charge').text('0.00')
				$('.payable').text('0.00')
				
			}
			
			$('.limit').text('limit : ' + min.toFixed(precesion) + ' ~ ' + max.toFixed(precesion) + ' ' + tarification.currency)
		}
		
        (function($) {

            var wallet = null;

            $('#wallet').on('change', function() {

                if ($(this).find('option:selected').val() == '') {
                    return false
                }

                wallet  =  $(this);

                var gateways = $(this).find('option:selected').data('gateways')
				var ugateways = unique_gateways(gateways);
				window['gateways']= gateways;//Gateway actullements disponible pour la devise
		
                var sym = $(this).find('option:selected').data('sym')
                var code = $(this).find('option:selected').data('code')
                var rate = $(this).find('option:selected').data('rate')
                
                $('.curr_code').text(code)
                $('.sym').text(sym)
                $('input[name=currency]').val(code)
                $('input[name=currency_id]').val($(this).find('option:selected').data('currency'))

                $('.gateway').removeAttr('disabled')
                $('.gateway').children().remove()
                var html = `<option value="">@lang('Select Gateway')</option>`;

                if (ugateways.length > 0) {
                    $.each(ugateways, function(i, val) {
						
                        html +=
                            ` <option data-alias="${val.gateway_alias}" data-max="${val.max_amount}" data-min="${val.min_amount}" data-fixcharge = "${val.fixed_charge}" data-percent="${val.percent_charge}" data-rate="${rate}" value="${val.method_code}">${val.name}</option>`
                    });
                    $('.gateway').append(html)
                    $('.gateway-msg').text('')

                } else {
                    $('.gateway').attr('disabled', true)
                    $('.gateway').append(html)
                    $('.gateway-msg').text('No gateway found with this currency.')
                }

            })

            $('.gateway').on('change', function() {
                
				if ($('.gateway option:selected').val() == '') {
                    $('.amount').attr('disabled', true)
                    $('.charge').text('0.00')
                    $('.payable').text(parseFloat($('.amount').val()))
                    $('.limit').text('limit : 0.00 USD')
                    return false
                }
        		window['gateway']= $('.gateway option:selected').data('alias')
				
				
                $('.amount').removeAttr('disabled')
				$('.amount').val(0)
				/*
                var amount = $('.amount').val() ? parseFloat($('.amount').val()) : 0;
                var code = $(wallet).find('option:selected').data('code')
	
                var type = $(wallet).find('option:selected').data('type')
				

                var rate = parseFloat($('.gateway option:selected').data('rate'))
                var min = parseFloat($('.gateway option:selected').data('min'))
                var max = parseFloat($('.gateway option:selected').data('max'))

                min = min/rate;
                max = max/rate;

                var fixed = parseFloat($('.gateway option:selected').data('fixcharge'))
                var pCharge = parseFloat($('.gateway option:selected').data('percent'))
                var percent = (amount * parseFloat($('.gateway option:selected').data('percent'))) / 100
             
                var totalCharge = fixed + percent
                var totalAmount = amount + totalCharge
                var precesion = 0;

                if (type == 1) {
                    precesion = 2;
                } else {
                    precesion = 8;
                }
                
                $('.charge').text(totalCharge.toFixed(precesion))
                $('.payable').text(totalAmount.toFixed(precesion))
                $('.limit').text('limit : ' + min.toFixed(precesion) + ' ~ ' + max.toFixed(precesion) + ' ' + code)

                $('.f_charge').text(fixed)
                $('.p_charge').text(pCharge)*/

            })

            $('.amount').on('keyup', function() {
                var amount = parseFloat($(this).val())
				var gateway = $(wallet).find('option:selected').data('code')
	
				var gateways = $(this).find('option:selected').data('gateways')
				facturation(amount,window['gateway'],window['gateways']);
			
				/*
                var type = $(wallet).find('option:selected').data('type')
                var code = $(wallet).find('option:selected').data('code')
                var fixed = parseFloat($('.gateway option:selected').data('fixcharge'))

                var percent = (amount * parseFloat($('.gateway option:selected').data('percent'))) / 100
                var totalCharge = fixed + percent
                var totalAmount = amount + totalCharge
                var precesion = 0;

                if (type == 1) {
                    precesion = 2;
                } else {
                    precesion = 8;
                }

                if (!isNaN(amount)) {
                    $('.show-amount').text(amount.toFixed(precesion))
                    $('.charge').text(totalCharge.toFixed(precesion))
                    $('.payable').text(totalAmount.toFixed(precesion))
                } else {
                    $('.show-amount').text('0.00')
                    $('.charge').text('0.00')
                    $('.payable').text('0.00')

                }*/
            })

            $('.req_confirm').on('click', function() {
                if ($('.amount').val() == '' || $('.gateway option:selected').val() == '' || $(wallet).find('option:selected').val() == '') {
                    notify('error', 'All fields are required')
                    return false
                }
				if(window['totalamount']<=0)
				{
					alert('Montant non prix en compte');
					return false;	
				}
				//return false;
                $('#form').submit()
                $(this).attr('disabled', true)
            })

        })(jQuery);
    </script>
@endpush
