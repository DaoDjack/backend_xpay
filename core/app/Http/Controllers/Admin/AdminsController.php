<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;
use App\Models\AdminNotification;
use App\Models\Agent;
use App\Models\ChargeLog;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\Withdrawal;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class AdminsController extends Controller{
 
   

    public function AdminsProfil (){
        $pageTitle = 'Profils utilisateurs';
        //choisir tous les profils
        $profils =  DB::select("select * from profils as a where statut  =1");
         
        return view('admin.system.profil',compact('pageTitle','profils'));
    }
    public function AdminsPassword ($login){
        $pageTitle = 'Password Setting';
        $admin = db_query("select * from admins as a where username  ='$login'");
        if(!empty($_POST['password']))
        {
            $query = "UPDATE admins set password = '".bcrypt($_REQUEST['password'])."',updated_at = '".datetime()."' where username = '$login'";
            db_query($query);
        }

        return view('admin.admins.password', compact('pageTitle', 'admin'));
    }
     
    public function Adminslist(){
       
        $pageTitle = "Admin user Dashboard";
        $histories =  DB::select("select admins.*,profils.libelle as profil from admins left join profils on profils.code = admins.role where  admins.statut  =1");
        $users  = to_array($histories);
       
        return view('admin.admins.liste', compact(
            'pageTitle', 'users')
        );

    }
   
    public function AdminsUpdate($user){

        $pageTitle = "Update user";
       
        if(!empty($_POST['email']))
        {
            $query = "UPDATE admins set email = '".$_POST['email']."',name = '".$_POST['name']."' where username = '$user'";
            db_query($query);
        }
        $profils =  DB::select("select * from profils as a where statut  =1");
        $profils = to_array($profils);
        $admin = db_query("select * from admins as a where username  ='$user'");
        $notify[] = ['success', 'Profile updated successfully'];
        return view('admin.admins.profile', compact('pageTitle', 'admin','profils'));
    }
    public function Adminsadd(){
        $pageTitle = 'Add Administrator';
        $admin = auth('admin')->user();
        $profils =  DB::select("select * from profils as a where statut  =1");
        $profils = to_array($profils);
        return view('admin.mprofile', compact('pageTitle', 'admin','profils'));
    }
    public function addAdmin(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'image' => ['nullable','image',new FileTypeValidate(['jpg','jpeg','png'])]
        ]);
        $exist =  DB::select("select * from admins as a where lower(username) = '".$request->name."' limit 1 ");
        if(empty($exist))
        {
            $old = md5(rand(10000,99999).'-'.rand(10000,99999)).".jpg";
            $query = "INSERT INTO `admins`(`id`, `name`, `email`, `username`, `role`, `email_verified_at`, `image`, `password`, `remember_token`, `created_at`, `updated_at`, `statut`) 
            VALUES ('','".$request->name."','".$request->email."','".$request->username."','".$request->role."','','".$old."','".bcrypt($request->password)."','','".datetime()."','".datetime()."','1')";
            DB::select($query);
    
            if ($request->hasFile('image')) {
                try {
    
                    $user->image = fileUploader($request->image, getFilePath('adminProfile'), getFileSize('adminProfile'), $old);
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Couldn\'t upload your image'];
                    return back()->withNotify($notify);
                }
            }
        }
        else
        {
            $notify[] = ['error', "Similar admin exists"];
            return back()->withNotify($notify);
        }
        

        $notify[] = ['success', 'Profile added successfully'];
        return to_route('admin.admins.list')->withNotify($notify);
    }
    public function AdminDelete ($username){
        $pageTitle = 'Password Setting';
        DB::select("DELETE FROM admins where lower(username) not in('admin','root','super','losale') and username='$username'");
        $pageTitle = "Admin user Dashboard";
        $histories =  DB::select("select admins.*,profils.libelle as profil from admins left join profils on profils.code = admins.role where  admins.statut  =1");
        $users  = to_array($histories);
       
        return view('admin.admins.liste', compact(
            'pageTitle', 'users')
        );
    }

    
    

}
