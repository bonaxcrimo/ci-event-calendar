<?php
class Roles extends MY_Controller{
    public function __construct(){
        parent::__construct();
        session_start();
        $this->load->model([
            'Mroles',
            'Mroles'
        ]);
    }
    /**
     * tampilan menu dari roles
     * @AclName menu Roles
     */
    public function index(){
        $this->render('roles/grid');
    }
    public function ajax_list(){
        $list = $this->Mroles->get_datatables();
        $data= array();
        $no = $_POST['start'];
        foreach($list as $roles){
            $no++;
            $row = array();
            $view = "<button class='btn btn-primary btn-sm' onclick=\"view('".$roles->roleid."')\" ".(hasPermission('roles','view')?'':'disabled')."><i class='fa fa-eye'></i></button> ";
            $edit = " <button class='btn btn-warning btn-sm' onclick=\"edit('".$roles->roleid."')\" ".(hasPermission('roles','edit')?'':'disabled')."><i class='fa fa-edit'></i></button> ";
            $del = " <button class='btn btn-danger btn-sm' onclick=\"deleted('".$roles->roleid."')\" ".(hasPermission('roles','delete')?'':'disabled')."><i class='fa fa-trash'></i></button> ";
            $aksi = $view.$edit.$del;
            $row[] = $no;
            $row[] = $aksi;
            $row[] = $roles->rolename;
            $row[] = $roles->modifiedby;
            $row[] = $roles->modifiedonview;
            $data[] = $row;
        }
        $output = array(
                        "draw" => @$_POST['draw'],
                        "recordsTotal" => $this->Mroles->count_all(),
                        "recordsFiltered" => $this->Mroles->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }
    /**
     * Fungsi view Roles
     * @AclName View Roles
     */
    public function view($id){
        $acosid = $this->Mroles->getList();
        $acos = $this->Mroles->getList();
        $data = $this->Mroles->getByIdRoles($id);
        if(empty($data)){
            redirect('roles');
        }
        $data->role_permission = strpos($data->acos,',')===false?[$data->acos]:explode(', ',$data->acos);
        $this->load->view('roles/view',['data'=>$data,'acos'=>$acos]);
    }
    /**
     * Fungsi tambah roles
     * @AclName Tambah Roles
     */
    public function add(){
        $data = [];
        $acos = $this->Mroles->getList();
        if($this->input->server('REQUEST_METHOD') == "POST"){
            if($this->_validateForm()){
                $data = $this->input->post();
                $this->_save($data);
                $error = 0;
                // redirect('roles');
            }else{
                $data = $this->input->post();
                $error = 1;
            }
            echo json_encode(['error'=>$error,'status'=>'sukses']);
        }else{
            $this->load->view('roles/form',['data'=>$data,'acos'=>$acos]);
        }

    }
    /**
     * Fungsi edit roles
     * @AclName Edit Roles
     */
    public function edit($id){
        $acos = $this->Mroles->getList();
        $data = $this->Mroles->getByIdRoles($id);
        if(empty($data)){
            redirect('roles');
        }
        $temp = $data;
        $data->role_permission = strpos($data->acos,',')===false?[$data->acos]:explode(', ',$data->acos);
        $error = 0;
        if($this->input->server('REQUEST_METHOD') == "POST"){
            if($this->_validateForm()){
                $data = $this->input->post();
                $data['roleid']=$id;
                $this->_save($data);
            }else{
                $data = $this->input->post();
                $error =1;
            }
            echo json_encode(['error'=>$error,'status'=>'sukses']);
        }else{
            $this->load->view('roles/form',['data'=>$data,'acos'=>$acos]);
        }
    }

    /**
     * Fungsi delete roles
     * @AclName Delete Roles
     */
    public function delete($id){
        $acos = $this->Mroles->getList();
        $data = $this->Mroles->getByIdRoles($id);
        if(empty($data)){
            redirect('roles');
        }
        $data->role_permission = strpos($data->acos,',')===false?[$data->acos]:explode(', ',$data->acos);
        $error = 0;
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $cek = $this->Mroles->delete($id);
            $error = $cek?0:1;
            $hasil = array(
                'error' => $error
            );
           echo json_encode(['error'=>$error,'status'=>'sukses']);
        }else{
            $this->load->view('roles/delete',['data'=>$data,'acos'=>$acos]);
        }

    }
    private function _save($data){
        return $this->Mroles->save($data);
    }
    private function _validateForm(){
        $rules = [
            [
                'field' => 'rolename',
                'label' => 'rolename',
                'rules' => 'trim|required|max_length[50]|callback_validateName'
            ]
        ];
        $this->form_validation->set_rules($rules);
        return $this->form_validation->run();
    }
    public function validateName($name){
        //ambil id dari url
        $id = $this->uri->segment('3');
        $exist = $this->Mroles->isNameExists($name, $id);
        if($exist === false){
            //nama tidak ada di table
            return true;
        }
        //nama ada kembalikan pesan error
        $this->form_validation->set_message(__FUNCTION__, "{field} '$name' is already exists.");
        return false;
    }
}