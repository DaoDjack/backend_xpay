@extends($activeTemplate.'layouts.user_master')

@section('content')
<div class="col-xl-10">
    <div class="card style--two">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-center">
            <div class="bank-icon  me-2">
                <i class="las la-money-bill"></i>
            </div>
            <h4 class="fw-normal">@lang('Transfer Money')</h4>
        </div>
        <div class="card-body p-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <form action="" method="POST" id="form">
                        @csrf
                        <input type="hidden" name="charge_id" value="">
                        <div class="d-widget">
                            <div class="d-widget__header">
                                <h6>@lang('Transfer Details')</h4>
                            </div>
                            <div class="d-widget__content px-5">
                                <div class="p-4 border mb-4">
                                    <div class="row">
                                        <div class="col-lg-12 form-group">
                                            <label class="mb-0">@lang('Your Wallet')</label>
                                            <select class="select style--two currency" name="wallet_id"  required>
                                                <option value="" selected>@lang('Select Wallet')</option>
                                                @foreach ($wallets as $wallet)
                                                    <option 
                                                        value="{{$wallet->id}}" 
                                                        data-code="{{$wallet->currency->currency_code}}" 
                                                        data-rate="{{$wallet->currency->rate}}" 
                                                        data-type="{{$wallet->currency->currency_type}}" 
                                                        {{ request('wallet') == $wallet->currency->currency_code ? 'selected' : '' }}
                                                    >
                                                    {{$wallet->currency->currency_code}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label class="charge" data-charge="{{json_encode($transferCharge)}}">
                                           @lang('Total Charge : ') <span class="total_charge">0.00</span>
                                        </label>
                                    </div><!-- row end -->
                                </div>
                                <div class="p-4 border mb-4">
                                    <div class="row">
                                        <div class="col-lg-6 form-group">
                                            <label class="mb-0">@lang('Amount to transfer')<span class="text--danger">*</span> </label>
                                            <input type="number" step="any" class="form--control style--two amount" name="amount" disabled placeholder="0.00" required value="{{old('amount')}}">
                                            <label>
                                                <span class="text--warning min">
                                                    @lang('Min:') --
                                                </span>
                                                <span class="text--warning max">
                                                    @lang('Max:')
                                                </span>
                                             </label>
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <label class="mb-0">@lang('Receiver Username / E-mail')<span class="text--danger">*</span></label>
                                            <div class="input-group align-items-center border-bottom">
                                                <input type="text" class="form--control style--two checkUser border-bottom-0" id="username" name="user" placeholder="@lang('Receiver Username / E-mail')" value="{{old('user')}}" required>
                                                <button type="button" class="input-text bg-transparent scan" data-toggle="tooltip" title="Scan QR">
                                                    <i class="las la-camera"></i>
                                                </button>
                                            </div>
                                            <label class="exist text-end"></label>
                                        </div>
                                    </div><!-- row end -->
                                </div>
                                @if($general->otp_verification && ($general->en || $general->sn || auth()->user()->ts))
                                    <div class="p-4 border mb-4">
                                        <div class="row">
                                            <div class="col-lg-12 form-group">
                                                @include($activeTemplate.'partials.otp_select')
                                            </div>

                                        </div><!-- row end -->
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-md btn--base mt-4 transfer w-100">@lang('Transfer Now')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="scanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <video id="preview" class="p-1 border" style="width:300px;"></video>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn--dark w-100 btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
    <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title">@lang('Transfer Money Preview')</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
            <div class="modal-body text-center p-0">
                <div class="d-widget border-start-0 shadow-sm">
                    <div class="d-widget__content">
                        <ul class="cmn-list-two text-center mt-4">
                            <li class="list-group-item">@lang('Transfer Amount'): <strong class="req_amount"></strong></li>
                            <li class="list-group-item">@lang('Total Charge'): <strong class="charge"></strong></li>
                            <li class="list-group-item">@lang('Payable'): <strong class="will_get"></strong></li>
                        </ul>
                    </div>
                    <div class="d-widget__footer text-center border-0 pb-3">
                        <button type="submit" class="btn btn-md w-100 d-block btn--base req_confirm" form="form">
                            @lang('Confirm') <i class="las la-long-arrow-alt-right"></i>
                        </button>
                    </div>
                </div>
            </div>
       </div>
    </div>
</div>

@endsection

@push('script')
<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

<script>
    (function ($) {
        'use strict'
		function getCharges()
		{
			var val = parseFloat($(".amount").val());
			var min,max;	
	
			for(var i in window['charges'])
			{
				min = parseFloat(window['charges'][i].min_limit);
				max = parseFloat(window['charges'][i].max_limit);
				
				if(min<=val && max>=val)
				{
					window['charge']= window['charges'][i];
					break;	
				}
			}	

		}
		window['charges']= $('.charge').data('charge');
		
        $('.scan').click(function(){
            var scanner = new Instascan.Scanner({ video: document.getElementById('preview'), scanPeriod: 5, mirror: false });
            scanner.addListener('scan',function(content){
                var route = "{{ route('qr.scan', '') }}"+"/"+content
                $.get(route, function( data ) {
                    if(data.error){
                        alert(data.error)
                    } else {
                        $("input[name=user]").val(data).focus();
                    }
                    $('#scanModal').modal('hide')
                });
            });

            Instascan.Camera.getCameras().then(function (cameras){
                if(cameras.length>0){
                    $('#scanModal').modal('show')
                        scanner.start(cameras[1]);
                } else{
                    alert('No cameras found.');
                }
            }).catch(function(e){
                alert('No cameras found.');
            });
        });

        $('.amount').on('input',function () {
			getCharges();
            if($(this).val() == ''){
                $('.total_charge').text('0.00')
            }
            else{
                var selected = $('.currency option:selected')
                if(selected.val()!=''){
                    var rate = selected.data('rate')
                    var code = selected.data('code')
                    var chargeData = $('.charge').data('charge')

                    var amount = $('.amount').val()

                    chargeCalc(amount,window['charge'],rate,code)
                }

            }
        })

        $('.currency').on('change', function () {
            var selected = $('.currency option:selected')
			
            if(selected.val()==''){
                $('.amount').attr('disabled',true)
                $('.total_charge').text('0.00')
                return false
            }
			$('.amount').val(0)
            $('.amount').attr('disabled',false)
            var rate = selected.data('rate')
            var code = selected.data('code')
            var type = selected.data('type')
            var chargeData = $('.charge').data('charge')
            var amount = $('.amount').val()
			
            chargeCalc(amount,window['charge'],rate,code)

            var min_limit = 0
            var max_limit = 0

            var min = min_limit/rate
            var max = max_limit/rate

            if(type == 1){
                var precision = 2
            } else {
                var precision = 4
            }

            $('.min').text("@lang('Min'): "+min.toFixed(precision)+' '+code+' -- ')
            $('.max').text("@lang('Max'): "+max.toFixed(precision)+' '+code)

        }).change();
		
		$('.amount').on('keyup',function () { 
			var selected = $('.currency option:selected')
			if(selected.val()!=''){
				var rate = selected.data('rate')
				var code = selected.data('code')
			}
		
			var val = parseFloat($(this).val());
			var min,max;	
			var trouve = false;
			for(var i in window['charges'])
			{
				min = parseFloat(window['charges'][i].min_limit)/rate;
				max = parseFloat(window['charges'][i].max_limit)/rate;
				
				if(min<=val && max>=val)
				{
					window['charge']= window['charges'][i];
					trouve = true;
					break;	
				}
			}
			chargeCalc(amount,window['charge'],rate,code)
            var rate  =  $('.currency option:selected').data('rate')
            var currCode  =  $('.currency option:selected').data('code')
            var type  =  $('.currency option:selected').data('type')
			if(!trouve)
			{
				window['charge']={};
			}
            var min_limit = parseFloat(window['charge'].min_limit)
            var max_limit = parseFloat(window['charge'].max_limit)
			
			if(isNaN(min_limit)) min_limit= 0;
			if(isNaN(max_limit)) max_limit= 0;
						
            var min = min_limit/rate
            var max = max_limit/rate
		

            if(typeof(rate) == 'undefined'){
                return false;
            }

            if(type==1){
                var precision = 2
            } else {
                var precision = 2
            }
            $('.min').text("@lang('Min'): "+min.toFixed(precision)+' '+currCode+' -- ')
            $('.max').text("@lang('Max'): "+max.toFixed(precision)+' '+currCode)

        })
        function chargeCalc(amount,chargeData,rate,code,$request = false) {
			
          	amount = parseFloat($('.amount').val())
			chargeData.percent_charge = parseFloat(chargeData.percent_charge);
			chargeData.fixed_charge = parseFloat(chargeData.fixed_charge);
			chargeData.cap = parseFloat(chargeData.cap);
			
			rate = parseFloat(rate);
		

		    var percentCharge = amount * chargeData.percent_charge/100;
            var cap = chargeData.cap/rate;
            var fixedCharge = chargeData.fixed_charge/rate;
            var totalCharge = fixedCharge+percentCharge;
			
            if(cap != 1 && totalCharge > cap){
                //totalCharge = cap
            }
			if(isNaN(totalCharge)) totalCharge= 0;
			if(isNaN(total)) total= 0;
			

            if($request){
                $('#confirm').find('.req_amount').text(amount+' '+code)
                $('#confirm').find('.charge').text(totalCharge.toFixed(2)+' '+code)
                var total = amount+totalCharge;
                $('#confirm').find('.will_get').text(total.toFixed(2)+' '+code)
                $('#confirm').modal('show')
            } else {
				
                $('.total_charge').text(totalCharge.toFixed(4)+' '+code)
            }

        }

        $('#form').on('submit', function(){

            var confirmMdoal = $('#confirm');

            if(!confirmMdoal.is(':visible')){
    
                var selected = $('.currency option:selected')
                if(selected.val() =='' || $('.amount').val() ==''){
                    $('.total_charge').text('0.00')
                    notify('error','Please fill up the fields first.')
                    return false
                }

                var rate = selected.data('rate')
                var code = selected.data('code')
                var chargeData = $('.charge').data('charge')
                var amount = parseFloat($('.amount').val())

                chargeCalc(amount,window['charge'],rate,code,true)

                confirmMdoal.modal('show');
                return false;
            }

        });

        $('.checkUser').on('focusout',function(e){
            var url = '{{ route('user.check.exist') }}';
            var value = $(this).val();
            var token = '{{ csrf_token() }}';

            if ($(this).attr('name') == 'user') {
                var data = {user:value,_token:token}
            }
            $.post(url,data,function(response) {
                if(response.own){
                    if($('.exist').hasClass('text--success')){
                        $('.exist').removeClass('text--success');
                    }
                    $('.exist').addClass('text--danger').text(response.own);
                    $('.transfer').attr('disabled',true)
                    return false
                }
                if(response['data'] != null){
                    if($('.exist').hasClass('text--danger')){
                        $('.exist').removeClass('text--danger');
                    }
                    $('.exist').text(`Valid user for transaction.`).addClass('text--success');
                    $('.transfer').attr('disabled',false)
                } else {
                    if($('.exist').hasClass('text--success')){
                        $('.exist').removeClass('text--success');
                    }
                    $('.exist').text('User doesn\'t  exists.').addClass('text--danger');
                }
            });
        });
        
        var old = @json(session()->getOldInput());
        if(old.length != 0){
            var old = @json(session()->getOldInput());
            $('input[name=user]').val(old.user);
            $('select[name=wallet_id]').val(old.wallet_id).change();
            $('input[name=amount]').val(old.amount);
            $('select[name=otp_type]').val(old.otp_type);
        }

    })(jQuery);
</script>
@endpush







