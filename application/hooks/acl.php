<?php
class ACL{
    private $CI;
    private $user;
    private static $instance;

    public function __construct(){
        $this->CI =& get_instance();
        self::$instance = $this;
    }
    public static function get_instance(){
        return self::$instance;
    }
    public function auth(){
        $CI = $this->CI;
        if($this->_checkQuestAccess()){
            return true;
        }
        $user = $this->getLoggedInUser();
        if(!empty($user) && @$_SESSION[SESSION_NAME.'versi']==SESSION_NAME){
        }else{
            $class = $CI->router->fetch_class();
            if($class!='login')
                redirect('login');
        }
        if(!$this->_validateActionPermission($user['acl']) && strtolower($_SESSION[SESSION_NAME.'username'])!='admin'){
            exit('anda tidak punya akses');
        }

    }
    public function getLoggedInUser(){
        $CI = $this->CI;
        $user = @$_SESSION[SESSION_NAME.'user'];

        if(!empty($this->user)){
            // print_r($this->user);
            return $this->user;
        }
        if(is_numeric($user)){
            $CI->load->model('Mlogin');
            $user = $CI->Mlogin->getDetailWithAclById($user);
        }
        $this->user = $user;
        return $user;
    }
    public function _checkQuestAccess($class = null,$method=null){

        $group = @$_SESSION[SESSION_NAME.'groups.guest'];
        if(empty($group)){
            $this->CI->load->model('Mroles');
            $group = $this->CI->Mroles->getGuestGroup();
            $_SESSION[SESSION_NAME.'groups.guest']=$group;
        }
        return $this->_validateActionPermission($group->acos,$class,$method);
    }
    private function _validateActionPermission($acos,$class = null,$method = null){
        //acos gk boleh kosong
        if(@$_SESSION[SESSION_NAME.'logged_in']!='' && @strtolower($_SESSION[SESSION_NAME.'username'])=='admin' && @$_SESSION[SESSION_NAME.'versi']==SESSION_NAME)
            return true;
        if(empty($class)){
            $class = $this->CI->router->fetch_class();
        }
        if(empty($method)){
            $method = $this->CI->router->fetch_method();
        }
        $class=strtolower($class);
        $method=strtolower($method);
        if($class=="login" || $class=="extension" || $class=="frontend"){
            return true;
        }
        if($_SESSION[SESSION_NAME.'logged_in']==null)
        {
            return false;
        }
        else{
            if($class=="home"  && @$_SESSION[SESSION_NAME.'versi']==SESSION_NAME){
                return true;
            }
        }
        if($method=='ajax_list'){
            if(hasPermission($class,'index')){
                return true;
            }
        }
        if(empty($acos) ){
            return false;
        }

        foreach($acos as $aco){
            if(strtolower(trim($aco['class'])) == strtolower(trim($class)) && strtolower(trim($aco['method']))==strtolower(trim($method))){
                return true;
            }
        }

        return false;
    }
    public function hasPermission($class,$method){
        if($this->_checkQuestAccess($class,$method)){
            return true;
        }
        $user = $this->user;
        if(!empty($user) && isset($user['acl'])){
            return $this->_validateActionPermission($user['acl'],$class,$method);
        }
        return false;
    }
}