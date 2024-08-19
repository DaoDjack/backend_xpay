@extends('admin.layouts.app')
@section('panel')

    <div class="row mb-none-30">
    <div class="col-xl-9 col-lg-10 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4 border-bottom pb-2">@lang('Profile Information')</h5>

                    <form action="{{ route('admin.admins.nouveau') }}" method="POST" enctype="multipart/form-data">
                        @csrf



                        <div class="row">

                            <div class="col-xl-6 col-lg-12 col-md-6">

                                <div class="form-group">
                                    <div class="image-upload">
                                        <div class="thumb">
                                            <div class="avatar-preview">
                                                <div class="profilePicPreview" style="background-image: url({{ getImage(getFilePath('adminProfile').'/'.$admin->image,getFileSize('adminProfile')) }})">
                                                    <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                            <div class="avatar-edit">
                                                <input type="file" class="profilePicUpload" name="image" id="profilePicUpload1" accept=".png, .jpg, .jpeg">
                                                <label for="profilePicUpload1" class="bg--success">@lang('Upload Image')</label>
                                                <small class="mt-2  ">@lang('Supported files'): <b>@lang('jpeg'), @lang('jpg').</b> @lang('Image will be resized into 400x400px') </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-xl-6 col-lg-12 col-md-6">
                             <div class="form-group ">
                                    <label>@lang('Profils utilisateurs')</label>
                                    <select class="form-control" type="text" name="role" required>
                                    <?php foreach($profils as $prof){?>
                                    <option value="<?php echo $prof['Code']; ?>"><?php echo $prof['Libelle']; ?></option>
                                    <?php }?>
                                    </select>
                                </div>
                            <div class="form-group ">
                                    <label>@lang('Login')</label>
                                    <input class="form-control" type="text" name="username" value="" required>
                                </div>
                                 <div class="form-group ">
                                    <label>@lang('Name')</label>
                                    <input class="form-control" type="text" name="name" value="" required>
                                </div>
                                <div class="form-group ">
                                    <label>@lang('Password')</label>
                                    <input class="form-control" type="text" name="password" value="" required>
                                </div>
                                
                                <div class="form-group ">
                                    <label>@lang('Telephone')</label>
                                    <input class="form-control" type="text" name="phone" value="" required>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Email')</label>
                                    <input class="form-control" type="email" name="email" value="" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
        

        
    </div>
@endsection

@push('breadcrumb-plugins')

@endpush
