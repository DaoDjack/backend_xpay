
<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{route('admin.dashboard')}}" class="sidebar__main-logo"><img src="{{getImage(getFilePath('logoIcon') .'/logo.png')}}" alt="@lang('image')"></a>
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
            
            
            	<!----- -->
                <?php foreach($_SESSION['sidebar'] as $section){
					
					if(!empty($section['Nom']))
					{
					?>
                
                <li class="sidebar__menu-header" <?php if(!in_array('users',$_SESSION['ymains'])){?>  <?php }?>><?php echo $section['Nom']; ?></li>
                <?php }
				foreach($section['menus'] as $menu){
					
					if(empty($menu['Menu'])) continue;
					if(!haveModule($menu['Code'])) continue;
					if(count($menu['Menu'])<=0)
					{
						
				 	?>
				 <li class="sidebar-menu-item "  >
                    <a href="javascript:void(0)" class="">
                        <i class="menu-icon las <?php echo $menu['Icone']; ?>"></i>
                        <span class="menu-title"><?php if(!empty($menu['Nom'])) echo $menu['Nom']; ?></span>
                    </a>
                    </li>
				 	<?php		
					}
					else
					{
					 ?>
				 <li class="sidebar-menu-item sidebar-dropdown <?php if($_SESSION['URI'][2]==$menu['Code']){?> active<?php }?> collapsed"  >
                    <a href="javascript:void(0)" class="">
                        <i class="menu-icon las <?php echo $menu['Icone']; ?>"></i>
                        <span class="menu-title"><?php if(!empty($menu['Nom'])) echo $menu['Nom']; ?></span>
                    </a>
                    <div class="sidebar-submenu  ">
                        <ul>
                        <?php foreach($menu['Menu'] as $sousmenu){  ?>
                            <li class="sidebar-menu-item ">
                                <a href="<?php echo admin_url($sousmenu['CodeMenu'].'/'.$sousmenu['Code']); ?>" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title"><?php echo $sousmenu['Nom']; ?></span>
                                </a>
                            </li>
                            <?php }?>
                           
                         
                        </ul>
                    </div>
                </li> 
				     <?php
					}
				?>
               
                <?php
				}//Fin menu
				  }?>
                <!----- -->
                
            </ul>
            <div class="text-center mb-3 text-uppercase">
                <span class="text--primary">{{__(systemDetails()['name'])}}</span>
                <span class="text--success">@lang('V'){{systemDetails()['version']}} </span>
            </div>
        </div>
    </div>
</div>
<!-- sidebar end -->

@push('script')
    <script>
        if($('li').hasClass('active')){
            $('#sidebar__menuWrapper').animate({
                scrollTop: eval($(".active").offset().top - 320)
            },500);
        }
    </script>
@endpush
