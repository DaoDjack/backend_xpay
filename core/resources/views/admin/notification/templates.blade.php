@extends('admin.layouts.app')
@section('panel')
<div class="row">
	<div class="col-lg-12">
    <div class="d-flex flex-wrap gap-3 mt-4">
            <?php if(haveAccess('notification','email')){?>
      <a href="<?php echo admin_url('notification/email/setting');?>" class="btn btn--primary btn--shadow  btn-lg">
          <i class="las la-envelope"></i>@lang('Email')
                  </a>
                <?php }?>
		<?php if(haveAccess('notification','sms')){?>
      <a href="<?php echo admin_url('notification/sms/setting');?>" class="btn btn--primary btn--shadow  btn-lg">
          <i class="las la-tablet"></i>@lang('Sms')
                  </a>
		<?php }?>

 		<?php if(haveAccess('notification','push')){?>
        <a href="<?php echo admin_url('notification/push/setting');?>" class="btn btn--primary btn--shadow  btn-lg">
            <i class="las la-hourglass"></i>@lang('Push')
        </a>
		<?php }?>
            </div>
            <hr />
        <div class="card">
            <div class="card-body px-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two custom-data-table">
                        <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Subject')</th>
                            <th>@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td>{{ __($template->name) }}</td>
                                <td>{{ __($template->subj) }}</td>
                                <td>
                                    <a href="{{ route('admin.setting.notification.template.edit', $template->id) }}"
                                        class="btn btn-sm btn-outline--primary ms-1 editGatewayBtn">
                                        <i class="la la-pencil"></i> @lang('Edit')
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse

                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
        </div><!-- card end -->
    </div>
</div>
@endsection
