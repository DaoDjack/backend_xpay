@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <ul class="list-group">
                    <?php foreach($profils as $profil){?>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <a href="{{ route('admin.system.profil.detail',$profil->Code) }}"><span> <?php echo $profil->Libelle; ?></span></a>
                        </li>
                        <?php }?>
                        
                  </ul>
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
