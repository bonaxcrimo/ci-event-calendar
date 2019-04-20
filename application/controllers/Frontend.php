<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Frontend extends MY_Controller {

    public function __construct(){
        parent::__construct();
        session_start();
    }
    public function index(){
        @$username = $_SESSION[SESSION_NAME.'username'];
        $data['username'] = $username;
        $this->load->view('home',$data);
    }
}
