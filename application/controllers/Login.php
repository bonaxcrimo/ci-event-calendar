 <?php

class Login extends MY_Controller {

	public function __construct() {
		parent::__construct();
        session_start();
		$this->load->model('mlogin');
	}

	public function index(){
        $adminId = @$_SESSION[SESSION_NAME.'logged_in'];
        $versi = @$_SESSION[SESSION_NAME.'versi'];
        if(!empty($adminId) and $versi==SESSION_NAME){
    		redirect('home/index');
        }
        $error = '';
        if($this->input->server('REQUEST_METHOD') == 'POST'){
        	$error = '';
            $data = $this->input->post();
        	$user = $this->mlogin->verifyLogin($data['userid'],$data['password']);
        	if(!empty($user)){
                $_SESSION[SESSION_NAME.'user'] = $user['userpk'];
                $_SESSION[SESSION_NAME.'userpk'] = $user['userpk'];
                $_SESSION[SESSION_NAME.'password'] = $user['password'];
                $_SESSION[SESSION_NAME.'username'] = $user['username'];
                $_SESSION[SESSION_NAME.'dashboard'] = $user['dashboard'];
                $_SESSION[SESSION_NAME.'logged_in'] = true;
                $_SESSION[SESSION_NAME.'versi'] = SESSION_NAME;
                $status="sukses";
                $msg="Anda Berhasil Login";
                redirect("home/index");
        	}else{
        		$msg = 'Username atau Password Salah';
                $msg = alert('error',$msg);
                $status="gagal";
                $this->load->view('login',['error'=>$msg]);
        	}
        }else{
            $this->load->view('login',['error'=>$error]);
        }
	}
	public function logout(){
        unset($_SESSION[SESSION_NAME.'user']);
        unset($_SESSION[SESSION_NAME.'userpk']);
        unset($_SESSION[SESSION_NAME.'password']);
        unset($_SESSION[SESSION_NAME.'username']);
        unset($_SESSION[SESSION_NAME.'dashboard']);
        unset($_SESSION[SESSION_NAME.'versi']);
        unset($_SESSION[SESSION_NAME.'logged_in']);
        unset($_SESSION[SESSION_NAME.'groups.guest']);
        unset($_SESSION[SESSION_NAME.'last_activity']);
        // session_unset();
        // session_destroy();
		redirect('login');
	}
}

