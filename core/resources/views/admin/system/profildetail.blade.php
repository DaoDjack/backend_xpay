
@extends('admin.layouts.app')
@section('panel')
<!-- Container fluid  -->

<div class="container-fluid">
    
    <!-- Bread crumb and right sidebar toggle -->
    
    
    
    <!-- End Bread crumb and right sidebar toggle -->
    

    
    <!-- Start Page Content -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-outline-info">
                <div class="card-header">
                    <h4 class="m-b-0 ">Définition des rôles pour : &laquo;<b><?php echo $profil['Libelle']; ?></b> &raquo;</h4>

                </div>
                <div class="card-body">
                    <form method="post" action="{{route('admin.system.profil.update')}}" class="form-horizontal" validate>
                     @csrf
                     <input type="hidden" name="code" id="code" class="" value="{{$profil['Code']}}">
                         <div class="row">
                         <?php foreach($menus as $menu){
							 $checked = '';
							 $allbase = 
							 $all = false;
							 $Base = array();
							 
							 
							foreach($menu['Base'] as $k=>$v)
							{
								if($v==1) $Base[]= $k;
							}
							if(empty($menu['Code'])) continue;
							
							 if(in_array('*',$menu['Droits'])) {$all =true; $checked = ' checked="checked"';}
							 if(in_array('*',$Base)) {$allbase =true; }
							 ?>
                         <div class="col-lg-3">
                         <div class="card">
                            <div class="card-body collapse show">
                                <h4 class="card-title"><fieldset>
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" value="1" name="ch_<?php echo $menu['Code'];?>_all" id="ch_<?php echo $menu['Code'];?>_all"  class="all custom-control-input"  <?php echo $checked; ?>    readonly="readonly" <?php if($allbase){?> onclick=" return false;" <?php }?>> <span class="custom-control-indicator"></span> <span class="custom-control-description text-dark" style="font-size:13px"><strong><?php if($allbase){?><u class="text-danger"><?php echo $menu['Nom'];?></u> <?php }else{?> <?php echo $menu['Nom'];?><?php }?></strong></span> </label>
                                    </fieldset></h4>
                                <?php foreach($menu['Menu'] as $smenu){
									
											 $checked = '';
											 if(in_array($smenu['Code'],$menu['Droits']) or $all) $checked = ' checked="checked"';
										
									?>
                                    <fieldset>
                                        <label class="custom-control custom-checkbox">
                                        <?php if(in_array($smenu['Code'],$Base) or $allbase){?>
                                         <i class="text-info align-middle" data-feather="check-square" style=" height:15px"></i><!--<input type="hiddenb" value="1" name="ch_<?php echo $menu['Code'];?>_<?php echo $smenu['Code'];?>" id="ch_<?php echo $menu['Code'];?>_<?php echo $smenu['Code'];?>"   class="fils custom-control-input text-info" <?php echo $checked; ?>>-->
                                        <?php }else{?>
                                         <input type="checkbox" value="1" name="ch_<?php echo $menu['Code'];?>_<?php echo $smenu['Code'];?>" id="ch_<?php echo $menu['Code'];?>_<?php echo $smenu['Code'];?>"   class="fils align-middle custom-control-input" <?php echo $checked; ?>>
                                        <?php }?>
                                             <span class="custom-control-indicator"></span> <small class="custom-control-description" style="font-size:11px"><?php echo $smenu['Nom'];?></small> </label>
                                    </fieldset>
                                  <?php }?>                  
                                                    
                            </div>
                        </div><hr /></div>
                        <?php }?>
                        <?php if($profil['Code']!='adminD'){?>
                        <div class="text-xs-right">
                                        <button type="submit" class="btn btn-info">Mettre à jour</button>

                        </div>
                        <?php }?>
                         </div>
                       <input type="hidden" name="" value="" /> 
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- End Page Content -->

</div>
<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
<script>
<!--
function changeprofil()
{
	var oui = confirm("Vous allez changer de profil.Ceci peut entraîner la disparition ou l'apparition de champ spécifique au profil");
	return oui;
}
function new_profil()
{
	var oui = confirm("Voulez vous changer le profil");
	if(oui) $('#profil').prop('disabled',false);
}
$( document ).ready(function() {
	
   $('.all').click
   (
   		function ()
		{
			var val = $(this).prop('checked');
			var menu = this.id;
			menu = menu.split('_');
			
			$('.fils').each(function(index) {
				var smenu = this.id;
				smenu = smenu.split('_');
				if(menu[1]==smenu[1])
				{
					$(this).prop('checked',val);
				}
			  });

		}
   );
});
-->
</script>
@endsection
@push('style')
<style>
  .list-group-item span{
    font-size: 22px !important;
    padding: 8px 0px
  }
</style>
@endpush