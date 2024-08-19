<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

$_SESSION['sidebar'] = array();
$ymains = array();
if(empty($_SESSION['maind']))
{
    //Obtenir le menu en fonctions
    
   
    if(!empty($_SESSION['username']) and getUri(1)=='admin')
    {
        if(empty($_SESSION['session'])) $_SESSION['session'] = array();
        if(empty($_SESSION['URI'][2])) $_SESSION['URI'][2] = '';

        if(empty($_SESSION['role']))
        {
            $role = DB::select("select role from admins  where username = '".$_SESSION['username']."' limit 1");
            if(empty($role[0]->role)) $role[0]->role = 'user';
            $_SESSION['role']=$role[0]->role;
        }
        $FREES_MODULES = array('dashboard','email','profile','password');
		
        $menus = DB::select("select men.* from admin_menus as men where men.etat = 1 order by ordre ASC, Nom ASC ");
        $menus = to_array($menus);
		foreach($menus as $men)
		{
			$mains[]= $men['Code'];
			$men['Menu'] = array();
			$men['Droits'] = array();
			$MENUS[$men['Code']]=$men;
		}
		
        $smenus = DB::select("select smen.* from admin_sousmenus as smen where smen.CodeMenu in ('".implode("','",$mains)."') and smen.etat =1 and smen.Direct = 1 order by ordre ASC, Nom ASC");
        $smenus = to_array($smenus);
		foreach($smenus as $smen)
		{
            $MENUS[$smen['CodeMenu']]['Menu'][]=$smen;
		}
        
        $role = DB::select("select role from admins where lower(username)  ='".$_SESSION['username']."'");
        if(empty($role)) 
        {
            $_SESSION['role']='';
            session_destroy();
        }
        else {
          $role = to_array($role[0]); 
          $_SESSION['role']= $role['role']; 
        }
        
        
        $droits = DB::select("select a.* from autorisations as a where codeprofil  ='".$_SESSION['role']."'");
        $droits = to_array($droits);
		
		$types = DB::select("select * from admin_types where etat = 1 order by ordre");
        $types = to_array($types);
        $ymains['dashboard']= '*';
       
		foreach($droits as $droit)
		{
			$MENUS[$droit['CodeMenu']]['Droits'][]=$droit['CodeSousMenu'];
          
            if($droit['CodeSousMenu']=='*')
            {
                $ymains[$droit['CodeMenu']] = '*';
                continue;
            }
            else
            {
                $ymains[$droit['CodeMenu']][]= $droit['CodeSousMenu'];

            }
            
		} 
       
        $_SESSION['ymains']= $ymains;
		$data = $_SESSION['session'];
        $NAV['rien']=array();
        foreach($types as $type)
		{
            $NAV[$type['Code']]=array();
        }
		foreach($MENUS as $k=>$valeur)
        {
            if(empty($valeur['CodeTypeMenu'])) $valeur['CodeTypeMenu']='rien';
            
            if(!empty($valeur['CodeTypeMenu'])) $NAV[$valeur['CodeTypeMenu']]['menus'][]=$valeur;
            
        }
        
        foreach($types as $type)
		{
            $NAV[$type['Code']]['Nom']=$type['Nom'];
            if(empty($NAV[$type['Code']]['menus'])) $NAV[$type['Code']]['menus']=array(); 
        }
        $_SESSION['sidebar']= $NAV;
           
		
		
		/*
		if(empty($jet))
		{
			$this->db->query("insert into jetons(iduser,jeton,dateexpiration) values ('".$data['id']."','$jeton','".datetime()."')");
		}
		else $this->db->query("update jetons set jeton ='$jeton',dateexpiration ='".datetime()."' where iduser ='".$data['id']."'");*/
		$data['menus'] = $MENUS;
		$data['access'] = false;
		$data['frees'] =$FREES_MODULES;

        $module = getUri(2);
        $action = getUri(3);
        if(empty($module)) $module = 'dashboard';
        $_SESSION['module']= $module;
        $_SESSION['action']= $action;
        $_SESSION['menus'] = $MENUS;
		//Verifier les accès !
        $_SESSION['access']=0;
        if(!empty($data['menus'][$module]) or $module=='dashboard')//Droits d'acces au module en cours
        {
            if(empty($data['menus'][$module]['Droits'])) $data['menus'][$module]['Droits'] = array();
            if(@in_array('*',$data['menus'][$module]['Droits']) or  @in_array($action,$data['menus'][$module]['Droits']) or  @in_array($module,$FREES_MODULES)) 
            {
                $current = $data['menus'][$module];
                
                
                $actions = DB::select("select smen.* from admin_sousmenus as smen where  smen.CodeMenu = '$module' and smen.etat =1 and (smen.Groupe = 1 or smen.Objet=1) order by ordre asc");
                $actions = to_array($actions);
                $nactions = array();
                foreach($actions as $act)
                {
                    /*if(@in_array('*',$this->session->menus[$this->uri->segment(2)]['Droits']) or  @in_array($act['Code'],$this->session->menus[$this->uri->segment(2)]['Droits']) or  @in_array($this->uri->segment(2),$FREES_MODULES)) 
                    {
                        $nactions[]=$act;
                    }*/
                }
                $data['actions'] = ($nactions);
                $_SESSION['access']=
                $data['access'] = true;
            }
            
            
            
        }
       
        else
        {
            //Ajouter ce module et cette fonctionnalité
           /*
            $query = "INSERT INTO `admin_menus`(`Code`, `Nom`, `Ordre`, `Icone`, `CodeTypeMenu`, `Etat`) VALUES ('$module','$module Nom','1','','','1')";
            
            DB::select($query);
            echo "Ce module sera désormais pris en compte";
          */
            //
        }
        //enregistrement des menus ete sous menus
       
        $menu = DB::select("select code from admin_menus  where code  ='".$_SESSION['URI'][2]."'");
        
        if(!empty($_SESSION['URI'][2]))
        {
            if(empty($menu))
            {
                $query = "INSERT INTO `admin_menus`(`Code`, `Nom`, `Ordre`, `Icone`, `CodeTypeMenu`, `Etat`) VALUES ('".$_SESSION['URI'][2]."','".$_SESSION['URI'][2]." Nom','1','','','1')";
                
                DB::select($query);
                
            }
           
        }
       
        $smenu = DB::select("select code from admin_sousmenus  where code  ='".$_SESSION['URI'][3]."'");
        if(empty($smenu))
        {
            $query = "INSERT INTO `admin_sousmenus` (`Code`, `CodeMenu`, `Nom`, `Icone`, `Direct`, `Objet`, `Groupe`, `Confirm`, `raccourci`, `Ordre`, `Etat`) VALUES ('".$_SESSION['URI'][3]."', '".$_SESSION['URI'][2]."', '".$_SESSION['URI'][3]." ".$_SESSION['URI'][2]."', 'fa fa-list-alt', '1', '0', '0', '0', '0', '2', '1')";
           DB::select($query);
            
        }

        
		
    }
	//exit();	
}
if(empty( $_SESSION['ymains']))  $_SESSION['ymains']= $ymains;


Route::namespace('Auth')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/', 'showLoginForm')->name('login');
        Route::post('/', 'login')->name('login');
        Route::get('logout', 'logout')->name('logout');
    });

    // Admin Password Reset
    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function(){
        Route::get('reset', 'showLinkRequestForm')->name('reset');
        Route::post('reset', 'sendResetCodeEmail');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });
 
    Route::controller('ResetPasswordController')->group(function () {
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
        Route::post('password/reset/change', 'reset')->name('password.change');
    });
});

Route::middleware('admin')->group(function () {
    Route::controller('AdminController')->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('transaction-detail-graph', 'trxDetailGraph')->name('trx.detail.graph');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');
        Route::get('access', 'access')->name('access');


        //Notification
        Route::get('notifications', 'notifications')->name('notifications');
        Route::get('notification/read/{id}', 'notificationRead')->name('notification.read');
        Route::get('notifications/read-all', 'readAll')->name('notifications.readAll');

        //Report Bugs
        Route::get('request-report', 'requestReport')->name('request.report');
        Route::post('request-report', 'reportSubmit');

        Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
    });

    // Manage Currencies 
    Route::controller('CurrencyController')->name('currency.')->prefix('currency')->group(function () {
        Route::get('/', 'allCurrency')->name('all');
        Route::post('add', 'add')->name('add');
        Route::post('update', 'update')->name('update');
        Route::post('api-key/update', 'updateApiKey')->name('api.update');
    });

    //Manage Transfer Charge
    Route::controller('TransactionChargeController')->name('transaction')->prefix('transaction')->group(function () {
        Route::get('/charges', 'manageCharges')->name('.charges');
		Route::get('/charges/detail', 'detailCharges')->name('.charges.detail');
		Route::get('/charges/delete', 'deleteCharges')->name('.charges.delete');
        Route::post('/charges/update', 'updateCharges')->name('.charges.update');
    });

    $manageGuardRoutes = function(){ 
        Route::get('/', 'allUsers')->name('all');
        Route::get('active', 'activeUsers')->name('active');
        Route::get('banned', 'bannedUsers')->name('banned');
        Route::get('email-verified', 'emailVerifiedUsers')->name('email.verified');
        Route::get('email-unverified', 'emailUnverifiedUsers')->name('email.unverified');
        Route::get('mobile-unverified', 'mobileUnverifiedUsers')->name('mobile.unverified');
        Route::get('kyc-unverified', 'kycUnverifiedUsers')->name('kyc.unverified');
        Route::get('kyc-pending', 'kycPendingUsers')->name('kyc.pending');
        Route::get('mobile-verified', 'mobileVerifiedUsers')->name('mobile.verified');
        Route::get('with-balance', 'usersWithBalance')->name('with.balance');
        Route::get('password/{id}', 'usersPassword')->name('password');
        Route::post('password/{id}', 'usersPassword')->name('password');
        Route::get('detail/{id}', 'detail')->name('detail');
        Route::get('kyc-data/{id}', 'kycDetails')->name('kyc.details');
        Route::post('kyc-approve/{id}', 'kycApprove')->name('kyc.approve');
        Route::post('kyc-reject/{id}', 'kycReject')->name('kyc.reject');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('add-sub-balance/{id}', 'addSubBalance')->name('add.sub.balance');
        Route::get('send-notification/{id}', 'showNotificationSingleForm')->name('notification.single');
        Route::post('send-notification/{id}', 'sendNotificationSingle')->name('notification.single');
        Route::get('login/{id}', 'login')->name('login');
        Route::post('status/{id}', 'status')->name('status');

        Route::get('send-notification', 'showNotificationAllForm')->name('notification.all');
        Route::post('send-notification', 'sendNotificationAll')->name('notification.all.send');
        Route::get('notification-log/{id}', 'notificationLog')->name('notification.log');
    };

    // Users Manager
    Route::group(['controller'=>'ManageGuardController', 'as'=>'users.', 'prefix'=>'users', 'guardType'=>'users'], $manageGuardRoutes);
    // Agent Manager   
    Route::group(['controller'=>'ManageGuardController', 'as'=>'agents.', 'prefix'=>'agents', 'guardType'=>'agents'], $manageGuardRoutes);
    // Merchant Manager   
    Route::group(['controller'=>'ManageGuardController', 'as'=>'merchants.', 'prefix'=>'merchants', 'guardType'=>'merchants'], $manageGuardRoutes);

    //Revenue Log 
    Route::controller('ProfitController')->name('profit.')->prefix('profit')->group(function () {
        Route::get('/all','allProfit')->name('all');
        Route::get('/only','onlyProfit')->name('only');
        Route::get('/commission','profitCommission')->name('commission');
        Route::get('/export-csv','profitExportCsv')->name('export.csv');
        Route::get('/search/','profitSearch')->name('search');
    });

    // Subscriber
    Route::controller('SubscriberController')->prefix('subscriber')->name('subscriber.')->group(function(){
        Route::get('/', 'index')->name('index');
        Route::get('send-email', 'sendEmailForm')->name('send.email');
        Route::post('remove/{id}', 'remove')->name('remove');
        Route::post('send-email', 'sendEmail')->name('send.email');
    });
 
    // Deposit Gateway
    Route::name('gateway.')->prefix('gateway')->group(function () {

        // Automatic Gateway
        Route::controller('AutomaticGatewayController')->prefix('automatic')->name('automatic.')->group(function(){
            Route::get('/', 'index')->name('index');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{code}', 'update')->name('update');
            Route::post('remove/{id}', 'remove')->name('remove');
            Route::post('status/{id}', 'status')->name('status');
        });

       
        // Manual Methods
        Route::controller('ManualGatewayController')->prefix('manual')->name('manual.')->group(function(){
            Route::get('/', 'index')->name('index');
            Route::get('new', 'create')->name('create');
            Route::post('new', 'store')->name('store');
            Route::get('detail/{alias}', 'detail')->name('detail');
		    Route::get('delete/{alias}', 'delete')->name('delete');
            Route::get('erase/{alias}', 'erase')->name('erase');
            Route::get('edit/{alias}', 'edit')->name('edit')->where('alias', '(.*)');
            Route::post('update/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
        });
    });

    // DEPOSIT SYSTEM
    Route::controller('DepositController')->prefix('deposit')->name('deposit.')->group(function(){
        Route::get('/', 'deposit')->name('list');
        Route::get('pending', 'pending')->name('pending');
        Route::get('rejected', 'rejected')->name('rejected');
        Route::get('approved', 'approved')->name('approved');
        Route::get('successful', 'successful')->name('successful');
        Route::get('initiated', 'initiated')->name('initiated');
        Route::get('details/{id}', 'details')->name('details');

        Route::post('reject', 'reject')->name('reject');
        Route::post('approve/{id}', 'approve')->name('approve');
    });

    // WITHDRAW SYSTEM
    Route::name('withdraw.')->prefix('withdraw')->group(function () {

        Route::controller('WithdrawalController')->group(function () {
            Route::get('pending', 'pending')->name('pending');
            Route::get('approved', 'approved')->name('approved');
            Route::get('rejected', 'rejected')->name('rejected');
            Route::get('log', 'log')->name('log');
            Route::get('details/{id}', 'details')->name('details');
            Route::post('approve', 'approve')->name('approve');
            Route::post('reject', 'reject')->name('reject');
        });


        // Withdraw Method
        Route::controller('WithdrawMethodController')->prefix('method')->name('method.')->group(function(){
            Route::get('/', 'methods')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::post('edit/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
        });
    });

    // Report
    Route::controller('ReportController')->prefix('report')->name('report.')->group(function(){
        Route::get('transaction/', 'transaction')->name('transaction');
        Route::get('login/history', 'loginHistory')->name('login.history');
        Route::get('login/ipHistory/{ip}', 'loginIpHistory')->name('login.ipHistory');
        Route::get('notification/history', 'notificationHistory')->name('notification.history');
        Route::get('email/detail/{id}', 'emailDetails')->name('email.details');
    });

    // Admin Support
    Route::controller('SupportTicketController')->prefix('ticket')->name('ticket.')->group(function(){
        Route::get('/', 'tickets')->name('index');
        Route::get('pending', 'pendingTicket')->name('pending');
        Route::get('closed', 'closedTicket')->name('closed');
        Route::get('answered', 'answeredTicket')->name('answered');
        Route::get('view/{id}', 'ticketReply')->name('view');
        Route::post('reply/{id}', 'replyTicket')->name('reply');
        Route::post('close/{id}', 'closeTicket')->name('close');
        Route::get('download/{ticket}', 'ticketDownload')->name('download');
        Route::post('delete/{id}', 'ticketDelete')->name('delete');
    });

    // Language Manager
    Route::controller('LanguageController')->prefix('language')->name('language.')->group(function(){
        Route::get('/', 'langManage')->name('manage');
        Route::post('/', 'langStore')->name('manage.store');
        Route::post('delete/{id}', 'langDelete')->name('manage.delete');
        Route::post('update/{id}', 'langUpdate')->name('manage.update');
        Route::get('edit/{id}', 'langEdit')->name('key');
        Route::post('import', 'langImport')->name('import.lang');
        Route::post('store/key/{id}', 'storeLanguageJson')->name('store.key');
        Route::post('delete/key/{id}', 'deleteLanguageJson')->name('delete.key');
        Route::post('update/key/{id}', 'updateLanguageJson')->name('update.key');
    });

    Route::controller('GeneralSettingController')->group(function () {
        // General Setting
        Route::get('general-setting', 'index')->name('setting.index');
        Route::post('general-setting', 'update')->name('setting.update');

        Route::get('setting/qr-code/template', 'qrCodeTemplate')->name('setting.qr.code.template');
        Route::post('setting/qr-code/template', 'qrCodeTemplateUpdate');

        //configuration
        Route::get('setting/system-configuration', 'systemConfiguration')->name('setting.system.configuration');
        Route::post('setting/system-configuration', 'systemConfigurationSubmit');

        // Logo-Icon
        Route::get('setting/logo-icon', 'logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'logoIconUpdate')->name('setting.logo.icon');

        //Custom CSS
        Route::get('custom-css', 'customCss')->name('setting.custom.css');
        Route::post('custom-css', 'customCssSubmit');

        //Cookie
        Route::get('cookie', 'cookie')->name('setting.cookie');
        Route::post('cookie', 'cookieSubmit');

        //maintenance_mode
        Route::get('maintenance-mode', 'maintenanceMode')->name('maintenance.mode');
        Route::post('maintenance-mode', 'maintenanceModeSubmit');
    });

    //Module setting
    Route::controller('ModuleSettingController')->group(function () {
        Route::get('module-setting', 'index')->name('module.setting');
        Route::post('module-setting/update', 'update')->name('module.update');
    });

    //KYC setting
    Route::controller('KycController')->group(function () {
        Route::get('kyc-setting/{kycType?}', 'setting')->name('kyc.setting');
        Route::post('kyc-setting-update', 'settingUpdate')->name('kyc.setting.update');
    });
 
    //Notification Setting
    Route::name('setting.notification.')->controller('NotificationController')->prefix('notification')->group(function () {
        //Template Setting
        Route::get('global', 'global')->name('global');
        Route::post('global/update', 'globalUpdate')->name('global.update');
        Route::get('templates', 'templates')->name('templates');
        Route::get('template/edit/{id}', 'templateEdit')->name('template.edit');
        Route::post('template/update/{id}', 'templateUpdate')->name('template.update');

        //Email Setting
        Route::get('email/setting', 'emailSetting')->name('email');
        Route::post('email/setting', 'emailSettingUpdate');
        Route::post('email/test', 'emailTest')->name('email.test');

        //SMS Setting
        Route::get('sms/setting', 'smsSetting')->name('sms');
        Route::post('sms/setting', 'smsSettingUpdate');
        Route::post('sms/test', 'smsTest')->name('sms.test');

        Route::get('push/setting','pushSetting')->name('push');
        Route::post('push/setting','pushSettingUpdate');
    });

    // Plugin
    Route::controller('ExtensionController')->prefix('extensions')->name('extensions.')->group(function(){
        Route::get('/', 'index')->name('index');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
    });

    //System Information
    Route::controller('SystemController')->name('system.')->prefix('system')->group(function () {
        Route::get('info', 'systemInfo')->name('info');
        Route::get('server-info', 'systemServerInfo')->name('server.info');
        Route::get('optimize', 'optimize')->name('optimize');
        Route::get('profil', 'profil')->name('profil');
        Route::get('profildetail/{id}', 'profilDetail')->name('profil.detail');
        Route::post('profilupdate', 'profilUpdate')->name('profil.update');
        Route::get('optimize-clear', 'optimizeClear')->name('optimize.clear');
    });

    //System Information
    Route::controller('AdminsController')->name('admins.')->prefix('admins')->group(function () {
        Route::get('list', 'Adminslist')->name('list');

        Route::get('password/{id}', 'AdminsPassword')->name('password');
        Route::post('password/{id}', 'AdminsPassword')->name('password');

        Route::get('delete/{id}', 'AdminDelete')->name('delete');

        Route::get('update/{id}', 'AdminsUpdate')->name('update');
        Route::post('update/{id}', 'AdminsUpdate')->name('update');

        Route::get('add', 'AdminsAdd')->name('add');
        Route::post('nouveau', 'addAdmin')->name('nouveau');
        Route::get('profil', 'AdminsProfil')->name('profil');
        
    });

    // SEO
    Route::get('seo', 'FrontendController@seoEdit')->name('seo');

    // Frontend
    Route::name('frontend.')->prefix('frontend')->group(function () {

        Route::controller('FrontendController')->group(function () {
            Route::get('templates', 'templates')->name('templates');
            Route::post('templates', 'templatesActive')->name('templates.active');
            Route::get('frontend-sections/{key}', 'frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'frontendElement')->name('sections.element');
            Route::post('remove/{id}', 'remove')->name('remove');
        });

        // Page Builder
        Route::controller('PageBuilderController')->group(function () {
            Route::get('manage-pages', 'managePages')->name('manage.pages');
            Route::post('manage-pages', 'managePagesSave')->name('manage.pages.save');
            Route::post('manage-pages/update', 'managePagesUpdate')->name('manage.pages.update');
            Route::post('manage-pages/delete/{id}', 'managePagesDelete')->name('manage.pages.delete');
            Route::get('manage-section/{id}', 'manageSection')->name('manage.section');
            Route::post('manage-section/{id}', 'manageSectionUpdate')->name('manage.section.update');
        });
    });
});