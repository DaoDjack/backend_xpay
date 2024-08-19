@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <form action="{{ route('admin.gateway.manual.update', $method->id) }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="payment-method-item">
                            <div class="payment-method-body">

                                <div class="row mt-4">
                                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-15">
                                        <div class="form-group">
                                            <label>@lang('Gateway Name')</label>
                                            <input type="text" class="form-control" name="name" value="{{ $method->name }}" readonly="readonly" required/>
                                             <input type="hidden" class="form-control" name="method_code" value="{{ $method->code }}" required/>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-15">
                                        <div class="form-group">
                                            <label>@lang('Currency')</label>
                                           
                                            <select name="lcurrency" class="form-control"  required  disabled="disabled">
                                                @foreach($currencies as $curr)
                                                    <option value="{{ $curr->currency_code }}" {{ $method->singleCurrency->currency == $curr->currency_code ? 'selected' : null }}>{{ __($curr->currency_code) }}</option>
                                                @endforeach
                                            </select>
                                            <input name="currency" type="hidden" value="{{$method->singleCurrency->currency}}" />
                                        </div>
                                    </div>
                                </div>


                                <div class="row">

                                    <div class="col-lg-6">
                                        <div class="card border--primary mt-3">
                                            <h5 class="card-header bg--primary">@lang('Range')</h5>
                                            <div class="card-body">
                                            <div class="input-group has_append mb-3">
                                            <input type="hidden" name="id" id="money_id" class="money " value="{{$moneyTransfer->id}}">
                                                <label class="w-100">@lang('Intervalle Charge') </label> <?php //echo $moneyList?>
                                                <select class="form-control" id="money"  onchange="selection(this);">
                                                    <?php foreach($moneyList as $money)
                                                    {
                                                        echo '<option value="'.$money['value'].'">'.$money['label'].'</option>';
                                                    }
                                                    ?>
                                                <option value="">[Add new transaction charge]</option>
												</select>
                                                
                                                    <div class="input-group-text">{{$method->singleCurrency->currency}}</div>
                                                   &nbsp; <button type="button"   class="btn btn--danger   mt-2" onclick="deleteCharges('money');">@lang('Remove')</button>
                                                </div>
                                                <div class="row">
                                                <div class="form-group col-lg-6">
                                                    <label>@lang('Minimum Amount')</label>
                                                    <div class="input-group">
                                                        <input type="number" step="any" class="money form-control" name="min_limit" id="money_min_amount" value="{{ getAmount(@$method->singleCurrency->min_amount) }}" required/>
                                                        <div class="input-group-text">{{$method->singleCurrency->currency}}</div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label>@lang('Maximum Amount')</label>
                                                    <div class="input-group">
                                                        <input type="number" step="any" class="money form-control" name="max_limit"  id="money_max_amount"value="{{ getAmount(@$method->singleCurrency->max_amount) }}" required/>
                                                        <div class="input-group-text">{{$method->singleCurrency->currency}}</div>
                                                    </div>
                                                </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="card border--primary mt-3">
                                            <h5 class="card-header bg--primary">@lang('Charge')</h5>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>@lang('Fixed Charge')</label>
                                                    <div class="input-group">
                                                        <input type="number" step="any" class="money form-control" name="fixed_charge"  id="money_fixed_charge"value="{{ getAmount(@$method->singleCurrency->fixed_charge) }}" required/>
                                                        <div class="input-group-text">{{$method->singleCurrency->currency}}</div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>@lang('Percent Charge')</label>
                                                    <div class="input-group">
                                                        <input type="number" step="any" class="money form-control" name="percent_charge"  id="money_percent_charge"value="{{ getAmount(@$method->singleCurrency->percent_charge) }}" required>
                                                        <div class="input-group-text">%</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="card border--primary mt-3">

                                            <h5 class="card-header bg--primary">@lang('Deposit Instruction')</h5>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <textarea rows="8" class="form-control border-radius-5 nicEdit" name="instruction">{{ __(@$method->description)  }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="card border--primary mt-3">
                                            <div class="card-header bg--primary d-flex justify-content-between">
                                                <h5 class="text-white">@lang('User Data')</h5>
                                                <button type="button" class="btn btn-sm btn-outline-light float-end form-generate-btn"> <i class="la la-fw la-plus"></i>@lang('Add New')</button>
                                            </div>
                                            <div class="card-body">
                                                <div class="row addedField">
                                                    @if($form)
                                                        @foreach($form->form_data as $formData)
                                                            <div class="col-md-4">
                                                                <div class="card border mb-3" id="{{ $loop->index }}">
                                                                    <input type="hidden" name="form_generator[is_required][]" value="{{ $formData->is_required }}">
                                                                    <input type="hidden" name="form_generator[extensions][]" value="{{ $formData->extensions }}">
                                                                    <input type="hidden" name="form_generator[options][]" value="{{ implode(',',$formData->options) }}">

                                                                    <div class="card-body">
                                                                        <div class="form-group">
                                                                            <label>@lang('Label')</label>
                                                                            <input type="text" name="form_generator[form_label][]" class="form-control" value="{{ $formData->name }}" readonly>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>@lang('Type')</label>
                                                                            <input type="text" name="form_generator[form_type][]" class="form-control" value="{{ $formData->type }}" readonly>
                                                                        </div>
                                                                        @php
                                                                            $jsonData = json_encode([
                                                                                'type'=>$formData->type,
                                                                                'is_required'=>$formData->is_required,
                                                                                'label'=>$formData->name,
                                                                                'extensions'=>explode(',',$formData->extensions) ?? 'null',
                                                                                'options'=>$formData->options,
                                                                                'old_id'=>'',
                                                                            ]);
                                                                        @endphp
                                                                        <div class="btn-group w-100">
                                                                            <button type="button" class="btn btn--primary editFormData" data-form_item="{{ $jsonData }}" data-update_id="{{ $loop->index }}"><i class="las la-pen"></i></button>
                                                                            <button type="button" class="btn btn--danger removeFormData"><i class="las la-times"></i></button>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-form-generator />
@endsection

@push('script')
    <script>
        "use strict"
        var formGenerator = new FormGenerator();
        formGenerator.totalField = {{ $form ? count((array) $form->form_data) : 0 }}
    </script>

    <script src="{{ asset('assets/global/js/form_actions.js') }}"></script>
@endpush



@push('breadcrumb-plugins')
<x-back route="{{ route('admin.gateway.manual.index') }}" />
@endpush

@push('script')
    <script>

        (function ($) {
            "use strict";

            $('input[name=currency]').on('input', function () {
                $('.currency_symbol').text($(this).val());
            });
            $('.currency_symbol').text($('input[name=currency]').val());

            @if(old('currency'))
            $('input[name=currency]').trigger('input');
            @endif
        })(jQuery);

    </script>
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
				url = "{{ route('admin.gateway.manual.detail',$alias) }}?id="+value;
				$('#money_id').val(value);
				
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
			  
			  url = "{{ route('admin.gateway.manual.delete',$alias) }}?id="+val;
			  
			  $.ajax({
			   type: "GET",
			   url: url,
			   success: function(msg)
					   {
							self.location = "{{ route('admin.gateway.manual.index') }}";
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
@endpush
