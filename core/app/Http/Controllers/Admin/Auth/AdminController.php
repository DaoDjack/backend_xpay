<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laramin\Utility\Onumoti;

class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = 'admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {   parent::__construct();
        $this->middleware('admin.guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function AdminProfil()
    {
        $pageTitle = "Admin Login";
        return view('admin.auth.login', compact('pageTitle'));
    }

    /**
     * Get the guard to be used during authentication.
     * 'Adminlist')->name('list');
        Route::get('password', 'AdminPassword')->name('password');
        Route::get('update', 'AdminUpdate')->name('update');
        Route::get('profil', 'AdminProfil')->name('profil');
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function AdminPassword()
    {
        return auth()->guard('admin');
    }

    public function AdminUpdate()
    {
        return 'username';
    }

    public function Adminlist()
    {
        $agent = agent();
        $pageTitle = "Agent Dashboard";
        $wallets = $agent->topTransactedWallets()->take(3)->with('currency')->get();  
        $totalAddMoney = $agent->totalDeposit();  
        $totalWithdraw = $agent->totalWithdraw();  
        $report = $agent->trxGraph();
     
        $userKyc = Form::where('act', 'agent_kyc')->first();
        $histories = Transaction::where('user_id', $agent->id)->where('user_type', 'AGENT')->with('currency', 'receiverUser')
                                ->orderBy('id', 'desc')->take(10)
                                ->get();

        $totalMoneyInOut = $agent->moneyInOut();
        $kyc = $agent->kycStyle();
     
        return view($this->activeTemplate . 'agent.dashboard', compact(
            'pageTitle', 'wallets', 'histories', 'totalMoneyInOut', 'userKyc', 'kyc', 'totalAddMoney',   'totalWithdraw', 'report', 'agent')
        );

    }

}
