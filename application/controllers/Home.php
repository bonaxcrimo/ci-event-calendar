<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

	public function __construct(){
		parent::__construct();
        session_start();
		$this->load->model('mlogin');
	}
	public function index(){
        $totalevents = $this->mlogin->getListAll('tblevents',[],true);
        $data['totalevents'] = $totalevents;
		$this->render($_SESSION[SESSION_NAME.'dashboard'],$data);
	}
}
