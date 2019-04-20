<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User extends MY_Controller {
    public function __construct(){
        parent::__construct();
        session_start();
        $this->load->model([
            'muser',
            'mroles'
        ]);
    }
    /**
     * tampilan menu dari user
     * @AclName menu User
     */
    public function index(){
        $this->render('user/grid');
    }
    public function ajax_list(){
        $list = $this->muser->get_datatables();
        $data= array();
        $no = $_POST['start'];
        foreach($list as $user){
            $no++;
            $row = array();
            $view = "<button class='btn btn-primary btn-sm' onclick=\"view('".$user->userpk."')\" ".(hasPermission('user','view')?'':'disabled')."><i class='fa fa-eye'></i></button> ";
            $edit = " <button class='btn btn-warning btn-sm' onclick=\"edit('".$user->userpk."')\" ".(hasPermission('user','edit')?'':'disabled')."><i class='fa fa-edit'></i></button> ";
            $del = " <button class='btn btn-danger btn-sm' onclick=\"deleted('".$user->userpk."')\" ".(hasPermission('user','delete')?'':'disabled')."><i class='fa fa-trash'></i></button> ";
            $aksi = $view.$edit.$del;
            if($user->roles_name=='SUPERADMIN'){
                $no--;
                continue;
            }
            $row[] = $no;
            $row[] = $user->userpk;
            $row[] = $aksi;
            $row[] = $user->userid;
            $row[] = $user->username;
            $row[] = $user->dashboard;
            $row[] = $user->roles_name;
            $row[] = $user->modifiedby;
            $row[] = $user->modifiedonview;
            $data[] = $row;
        }
        $output = array(
                        "draw" => @$_POST['draw'],
                        "recordsTotal" => $this->muser->count_all(),
                        "recordsFiltered" => $this->muser->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }
    public function getRoles(){
        $role = $this->db->get('tblroles')->result();
        echo json_encode($role);
    }
    /**
     *  view user
     * @AclName View User
     */
    public function view($id){
        $data =  $this->muser->getByIdUser($id);

        if(empty($data)){
            redirect('user');
        }
        $data->user_roles = strpos($data->roles,',')===false?$data->roles:explode(', ',$data->roles);
        $this->load->view('user/view',['data'=>$data]);
    }
    /**
     *  tambah user
     * @AclName Tambah User
     */
    public function add(){
        $data=[];
        $roles = $this->mroles->getList();
        $tmp = [];
        foreach($roles as $role){
            $tmp[$role->roleid] = $role->rolename;
        }
        $roles = $tmp;
        unset($tmp);
        if($this->input->server('REQUEST_METHOD')=='POST'){
            $data = $this->input->post();
            $cek=$this->_save($data);
            $status = $cek?"sukses":"gagal";
            $hasil = array(
                'status' => $status
            );
            echo json_encode($hasil);
        }else{
            $this->load->view('user/form',['data'=>$data,'roles'=>$roles]);
        }

    }
    /**
     *  edit user
     * @AclName Edit User
     */
    public function edit($id){
        $data=[];
        $roles = $this->mroles->getList();
        $tmp = [];
        foreach($roles as $role){
            $tmp[$role->roleid] = $role->rolename;
        }
        $roles = $tmp;
        unset($tmp);
        $data =  $this->muser->getByIdUser($id);

        if(empty($data)){
            redirect('user');
        }
        $data->user_roles = strpos($data->roles,',')===false?$data->roles:explode(', ',$data->roles);
        if($this->input->server('REQUEST_METHOD')=='POST'){
            $data = $this->input->post();
            $data['userpk'] = $id;
            if($data['password']=='')
                unset($data['password']);
            $cek = $this->_save($data);
            $status = $cek?"sukses":"gagal";
            $hasil = array(
                'status' => $status
            );
            echo json_encode($hasil);
            // redirect('user');
        }else{
            $this->load->view('user/form',['data'=>$data,'roles'=>$roles]);
        }

    }
    /**
     * Fungsi delete user
     * @AclName Delete User
     */
    public function delete($id){
       $data = $this->muser->getByIdUser($id);
        if(empty($data)){
            redirect('user');
        }
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $cek = $this->muser->delete($this->input->post('userpk'));
            $status = $cek?"sukses":"gagal";
            $hasil = array(
                'status' => $status
            );
            echo json_encode($hasil);
        }else{
            $this->load->view('user/delete',['data'=>$data]);
        }

    }
    private function _save($data){
        return $this->muser->save($data);
    }


}