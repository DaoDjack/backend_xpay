<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    public function systemInfo(){
        $laravelVersion = app()->version();
        $timeZone = config('app.timezone');
        $pageTitle = 'Application Information';
        return view('admin.system.info',compact('pageTitle', 'laravelVersion','timeZone'));
    }

    public function optimize(){
        $pageTitle = 'Clear System Cache';
        return view('admin.system.optimize',compact('pageTitle'));
    }
    public function profil(){
        $pageTitle = 'Profils utilisateurs';
        //choisir tous les profils
        $profils =  DB::select("select * from profils as a where statut  =1");
         
        return view('admin.system.profil',compact('pageTitle','profils'));
    }
    public function profilupdate(){
        
       
   

        $code = $_REQUEST['code'];

        $breaks = array();
        
        
        foreach($_POST as $k=>$v)
        {
            if(substr($k,0,3)=='ch_')
            {
                $vals = explode('_',$k);
                print_r($vals);
                if(in_array($vals[1],$breaks)) continue;
                if($vals[2]=='all')
                {
                    $breaks[]= $vals[1];
                    $vals[2]= '*';
                }
            if(!empty($vals[3])) $vals[2] .= '_'.$vals[3];

                $adds[]= "('".$vals[1]."', '".$vals[2]."', '1', '1', '".$code."')";
            }
        }
        $query = "DELETE FROM autorisations where codeprofil ='$code'";
        //$query = "DELETE FROM autorisations where codeprofil ='$code' and basic='0'";
        DB::update($query);
        
        $query = "INSERT INTO  `autorisations` (`CodeMenu`, `CodeSousMenu`, `Global`, `Etat`, `CodeProfil`) VALUES ".implode(',',$adds);
        
        DB::update($query);
        
        $notify = 'Rôles modifiés avec succès !';
      
        return redirect('admin/system/profildetail/'.$code);
       
    }
    public function profildetail($code){
        $pageTitle = 'Details des droits utilisateurs';
        //choisir tous les profils
        $menus =  DB::select("select men.* from admin_menus as men where men.etat = 1 order by ordre asc ");
        $menus = to_array($menus);
       

        foreach($menus as $men)
		{
			$mains[]= $men['Code'];
			$men['Menu'] = Array();
			$men['Droits'] = Array();
            $men['Base'] = Array();
			$MENUS[$men['Code']]=$men;
		}
		
		
        $smenus =  DB::select("select smen.* from admin_sousmenus as smen where smen.Codemenu in ('".implode("','",$mains)."') ");
        $smenus = to_array($smenus);
        
		foreach($smenus as $smen)
		{
			$MENUS[$smen['CodeMenu']]['Menu'][]=$smen;
		}
		
		
		$droits =DB::select("select a.* from autorisations as a where codeprofil ='$code'");;
        $droits = to_array($droits);
       
		foreach($droits as $droit)
		{
			$MENUS[$droit['CodeMenu']]['Droits'][]=$droit['CodeSousMenu'];
			$MENUS[$droit['CodeMenu']]['Base'][$droit['CodeSousMenu']]=$droit['Basic'];
		}
        $menus = $MENUS;

        $profils =  DB::select("select p.* from profils as p where code = '$code' ");
        $profil = to_array($profils[0]);
       
        return view('admin.system.profildetail',compact('pageTitle','profil','menus'));
    }

    public function optimizeClear(){
        Artisan::call('optimize:clear');
        $notify[] = ['success','Cache cleared successfully'];
        return back()->withNotify($notify);
    }

    public function systemServerInfo(){
        $currentPHP = phpversion();
        $pageTitle = 'Server Information';
        $serverDetails = $_SERVER;
        return view('admin.system.server',compact('pageTitle', 'currentPHP', 'serverDetails'));
    }
}
