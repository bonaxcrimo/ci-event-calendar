<?php
class UserAcl extends MY_Controller{
    public function __construct(){
        parent::__construct();
        session_start();
        $this->load->model([
            'Museracl',
            'Macos',
            'Mroles'
        ]);
    }
    public function ajax_list($userpk){
        $list = $this->Museracl->get_datatables($userpk);
        $data= array();
        $no = $_POST['start'];
        foreach($list as $acl){
            $no++;
            $row = array();
            $view = "<button class='btn btn-primary btn-sm' onclick=\"viewAcl('".$acl->useraclid."')\" ".(hasPermission('user','view')?'':'disabled')."><i class='fa fa-eye'></i></button> ";
            $aksi = $view;
            $route = count(getTableWhere('tblacos',["acosid"=>$acl->acoid]))>0?getTableWhere('tblacos',["acosid"=>$acl->acoid])[0]:'-';
            $acl->acoid = $route!='-'?$route->class."/".$route->method:$route;
            $row[] = $no;
            $row[] = $aksi;
            $row[] = $acl->acoid;
            $row[] = $acl->modifiedby;
            $row[] = $acl->modifiedonview;
            $data[] = $row;
        }
        $output = array(
                "draw" => @$_POST['draw'],
                "recordsTotal" => $this->Museracl->count_all($userpk),
                "recordsFiltered" => $this->Museracl->count_filtered($userpk),
                "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }
    /**
     *  view user
     * @AclName View User
     */
    public function view($id){
        $row =  $this->Museracl->getById('tbluseracl','useraclid',$id);
        $route = count(getTableWhere('tblacos',["acosid"=>$row->acoid]))>0?getTableWhere('tblacos',["acosid"=>$row->acoid])[0]:'-';
        $row->acoid = $route!='-'?$route->class."/".$route->method:$route;
        $data['row']=$row;

        if(empty($row)){
            redirect('user');
        }
        $this->load->view('user/useracl/view',$data);
    }


    /**
     * Fungsi delete parameter
     * @AclName Delete parameter
     */
    public function delete($id){
        $row = $this->Museracl->getById('tbluseracl','useraclid',$id);
        $route = count(getTableWhere('tblacos',["acosid"=>$row->acoid]))>0?getTableWhere('tblacos',["acosid"=>$row->acoid])[0]:'-';
        $row->acoid = $route!='-'?$route->class."/".$route->method:$route;
        $data['row']=$row;
        if(empty($row)){
            redirect('user');
        }
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            // $cek = $this->mparameter->delete($this->input->post('parameter_key'));
            // $status = $cek?"sukses":"gagal";
            // $hasil = array(
            //     'status' => $status
            // );
            // echo json_encode($hasil);
        }else{
             $this->load->view('user/useracl/delete',$data);
        }

    }
    private function _save($data){
        return $this->Museracl->save($data);
    }
    public function index(){
        @$userpk = $_GET['userpk'];
        if(empty($userpk)){
            echo "Empty";
        }
        else{
            $data['userpk'] = $userpk;
            $this->load->view('user/griduseracl',$data);
        }
    }
    public function edit($userpk=null){
        if(empty($userpk)){
            redirect('user');
        }
        $acos = $this->Macos->getList();
        $data = $this->Museracl->getByIdUser($userpk);
        $error = 0;
        if($this->input->server('REQUEST_METHOD') == "POST"){
            $data = $this->input->post();
            $data['userpk']=$userpk;
            $save = $this->_save($data);
            $status = "batal";
            if($save){
                $status="sukses";
            }
            echo json_encode(['status'=>$status]);
        }else{
            $this->load->view('user/useracl/form',['data'=>$data,'acos'=>$acos,'userpk'=>$userpk]);
        }
    }

    private function _validateForm(){
        $rules = [
            [
                'field' => 'role_permission[]',
                'label' => 'Roles',
                'rules' => 'required|numeric'
            ]
        ];
        $this->form_validation->set_rules($rules);
        return $this->form_validation->run();
    }
    public function grid($userpk){
        @$page = $_POST['page'];
        @$limit = $_POST['rows']?$_POST['rows']:10;
        @$sidx = $_POST['sidx'];
        @$sord = $_POST['sord'];
        if (!$sidx)
            $sidx = 1;
        @$totalrows = isset($_POST['totalrows']) ? $_POST['totalrows'] : false;
        if (@$totalrows) {
           @$limit = $totalrows;
        }
        @$filters = $_POST['filters'];
        @$search = $_POST['_search'];
        $where1=" where userpk = $userpk ";
        $where2="";

        if($search== "true") {
            $where2 = " AND (".$this->operation($filters).")";
        }
        $where = $where1." ".$where2;

        $sql = $this->Museracl->count($where);
        $count = $sql->num_rows();
        if ($count > 0) {
            @$total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            @$page = $total_pages;
        if ($limit < 0)
            @$limit = 0;
        $start = $limit * $page - $limit;
        if ($start < 0)
            @$start = 0;

        $data = $this->Museracl->get($where, $sidx, $sord, $limit, $start);
        $_SESSION[SESSION_NAME.'exceluseracl']= $sord."|".$sidx."|".$where;
        @$responce->page = $page;
        @$responce->total = $total_pages;
        @$responce->records = $count;
        @$responce->allData =$this->Museracl->get($where, $sidx, $sord, $limit, $start)->result();
        $i=0;
        foreach($data->result() as $row){
            $view='';
            $edit='';
            $del='';
            $view = hasPermission('useracl','view')?'<a href="#" onclick="view(\''.$row->useraclid.'\')" title="View"  class="btnview" style="float:left"><span class="ui-icon ui-icon-document"></span></a>':'<span style="float:left" class="ui-state-disabled ui-icon ui-icon-document"></span>';
            $row->aksi =$view.$edit.$del;
            $route = count(getTableWhere('tblacos',["acosid"=>$row->acoid]))>0?getTableWhere('tblacos',["acosid"=>$row->acoid])[0]:'-';

            $row->acoid = $route!='-'?$route->class."/".$route->method:$route;
            $responce->rows[$i]['id']   = $row->useraclid;
            $responce->rows[$i]['cell'] = array(
                $row->aksi,
                $row->acoid,
                $row->modifiedby,
                $row->modifiedonview
                );
            $i++;
        }
        echo json_encode($responce);
    }
     function operation($filters){
        $filters = str_replace('\"','"' ,$filters);
        $filters = str_replace('"[','[' ,$filters);
        $filters = str_replace(']"',']' ,$filters);
        $filters = json_decode($filters);
        $where = " ";
        $whereArray = array();
        $rules = $filters->rules;
        $groupOperation = $filters->groupOp;
        foreach($rules as $rule) {
            $fieldName = $rule->field;
            $fieldData = escapeString($rule->data);
                switch ($rule->op) {
                    case "eq": $fieldOperation = " = '".$fieldData."'"; break;
                    case "ne": $fieldOperation = " != '".$fieldData."'"; break;
                    case "lt": $fieldOperation = " < '".$fieldData."'"; break;
                    case "gt": $fieldOperation = " > '".$fieldData."'"; break;
                    case "le": $fieldOperation = " <= '".$fieldData."'"; break;
                    case "ge": $fieldOperation = " >= '".$fieldData."'"; break;
                    case "nu": $fieldOperation = " = ''"; break;
                    case "nn": $fieldOperation = " != ''"; break;
                    case "in": $fieldOperation = " IN (".$fieldData.")"; break;
                    case "ni": $fieldOperation = " NOT IN '".$fieldData."'"; break;
                    case "bw": $fieldOperation = " LIKE '".$fieldData."%'"; break;
                    case "bn": $fieldOperation = " NOT LIKE '".$fieldData."%'"; break;
                    case "ew": $fieldOperation = " LIKE '%".$fieldData."'"; break;
                    case "en": $fieldOperation = " NOT LIKE '%".$fieldData."'"; break;
                    case "cn": $fieldOperation = " LIKE '%".$fieldData."%'"; break;
                    case "nc": $fieldOperation = " NOT LIKE '%".$fieldData."%'"; break;
                    default: $fieldOperation = ""; break;
                }
            if($fieldOperation != "") {
                if($fieldName=="modifiedon"){
                    $whereArray[] = "DATE_FORMAT(modifiedon,'%d-%m-%Y %T')".$fieldOperation;
                }else if($fieldName=="modifiedby"){
                    $whereArray[] = "modifiedby".$fieldOperation;
                }
                else{
                    $whereArray[] = $fieldName.$fieldOperation;
                }
            }
        }

        if (count($whereArray)>0) {
            $where .= join(" ".$groupOperation." ", $whereArray);
        } else {
            $where = " ";
        }
        return $where;
    }
}