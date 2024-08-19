@extends('admin.layouts.app')
@section('panel')
     <div class="col-lg-12">
     <div class="d-flex flex-wrap gap-3 mt-4 col-lg-2">
                
                <div class="flex-fill">
                    <a href="<?php echo admin_url('admins/add'); ?>" class="btn btn--primary btn--shadow w-100 btn-lg">
                        <i class="las la-list-alt"></i>Ajouter                     </a>
                </div>

                

                

                                
                
                
            </div>
     
     <hr />

            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>Administrateurs</th>
                                <th>Email-Phone</th>
                                <th>Derniere action</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($users as $user){?>
                                                        <tr>
                                <td>
                                    <span class="fw-bold"><?php echo $user['name']; ?></span>
                                    <br>
                                    <span class="small">                                    <?php echo $user['username']; ?>
                                    </span>
                                </td>


                                <td>
                                   <?php echo $user['email']; ?>
                                </td>
                                <td>
                                   <?php echo $user['updated_at']; ?>
                                </td>



                                <td>
                                   <?php echo $user['profil']; ?>
                                </td>
                                <td>&nbsp;</td>

                                <td>
                                 <a href=<?php echo admin_url('admins/update/'.$user['username']); ?>  class="btn btn-sm btn-outline--primary">
                                        <i class="las la-pen"></i> Update                                   </a>
                                    <a href=<?php echo admin_url('admins/password/'.$user['username']); ?> class="btn btn-sm btn-outline--secondary">
                                        <i class="las la-key"></i> Password                                    </a>
                                        <a href=<?php echo admin_url('admins/delete/'.$user['username']); ?> onclick="return confirm('Do you want delete this account ?');" class="btn btn-sm btn-outline--danger">
                                        <i class="las la-trash"></i> Delete                                   </a>
                                       
                                                                    </td>

                            </tr>
                            <?php }?>
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                            </div>
        </div>
@endsection
@push('style')
<style>
  .list-group-item span{
    font-size: 22px !important;
    padding: 8px 0px
  }
</style>
@endpush
