<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parameter extends MY_Controller {

	public function __construct(){
		parent::__construct();
		session_start();
		$this->load->model('mparameter');
	}
	/**
     * Fungsi menu parameter
     * @AclName menu parameter
     */
	public function index(){
		$link = base_url().'parameter/grid';
		$this->render('parameter/gridparameter');
	}
    public function ajax_list(){
        $list = $this->mparameter->get_datatables();
        $data= array();
        $no = $_POST['start'];
        foreach($list as $parameter){
            $no++;
            $row = array();
            $view = "<button class='btn btn-primary btn-sm' onclick=\"view('".$parameter->parameter_key."')\" ".(hasPermission('parameter','view')?'':'disabled')."><i class='fa fa-eye'></i></button> ";
            $edit = " <button class='btn btn-warning btn-sm' onclick=\"edit('".$parameter->parameter_key."')\" ".(hasPermission('parameter','edit')?'':'disabled')."><i class='fa fa-edit'></i></button> ";
            $del = " <button class='btn btn-danger btn-sm' onclick=\"deleted('".$parameter->parameter_key."')\" ".(hasPermission('parameter','delete')?'':'disabled')."><i class='fa fa-trash'></i></button> ";
            $aksi = $view.$edit.$del;
            $row[] = $no;
            $row[] = $aksi;
            $row[] = $parameter->parametergrpid;
            $row[] = $parameter->parameterid;
            $row[] = $parameter->parametertext;
            $row[] = $parameter->parametermemo;
            $row[] = $parameter->modifiedby;
            $row[] = $parameter->modifiedonview;
            $data[] = $row;
        }
        $output = array(
                        "draw" => @$_POST['draw'],
                        "recordsTotal" => $this->mparameter->count_all(),
                        "recordsFiltered" => $this->mparameter->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }
	public function grid(){
		@$page = $_POST['page'];
        @$limit = $_POST['rows'];
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
        $where1=" where 1=1 ";
        $where2="";

        if($search== "true") {
            $where2 = " AND (".$this->operation($filters).")";
        }
        $where = $where1." ".$where2;

        $sql = $this->mparameter->count($where);
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

        $data = $this->mparameter->get($where, $sidx, $sord, $limit, $start);
        $_SESSION['excelparameter']= $sord."|".$sidx."|".$where;
        @$responce->page = $page;
        @$responce->total = $total_pages;
        @$responce->records = $count;
        $i=0;
        foreach($data->result() as $row){
        	$view='';
        	$edit='';
        	$del='';
            $view = hasPermission('parameter','view')?'<a href="#" onclick="viewData(\''.$row->parameter_key.'\')" title="View"  class="btnview" style="float:left"><span class="ui-icon ui-icon-document"></span></a>':'<span style="float:left" class="ui-state-disabled ui-icon ui-icon-document"></span>';
			$edit = hasPermission('parameter','edit')?'<a href="#" onclick="editData(\''.$row->parameter_key.'\')" title="Edit"  class="btnedit" style="float:left"><span class="ui-icon ui-icon-pencil"></span></a>':'<span style="float:left" class="ui-state-disabled ui-icon ui-icon-pencil"></span>';
			$del = hasPermission('parameter','delete')?'<a href="#" onclick="deleteData(\''.$row->parameter_key.'\')" title="Delete"  class="btndel" style="float:left"><span class="ui-icon ui-icon-trash"></span></a>':'<span style="float:left" class="ui-state-disabled ui-icon ui-icon-trash"></span>';
            $row->aksi =$view.$edit.$del;
            $responce->rows[$i]['id']   = $row->parameter_key;
            $responce->rows[$i]['cell'] = array(
                $row->parameter_key,
                $row->aksi,
                $row->parametergrpid,
                $row->parameterid,
                $row->parametertext,
                $row->parametermemo,
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

	/**
     * Fungsi view parameter
     * @AclName View parameter
     */
	public function view($parameter_key=0){
		$data['data'] = $this->mparameter->getById('tblparameter','parameter_key',$parameter_key);
		$this->load->view('parameter/view',$data);
	}
	/**
     * Fungsi add parameter
     * @AclName Tambah parameter
     */
	public function add(){
		$data=[];
		if($this->input->server('REQUEST_METHOD') == 'POST' ){
			$data = $this->input->post();
			$cek = $this->_save($data);
			$status = $cek?"sukses":"gagal";
			$hasil = array(
		        'status' => $status
		    );
		    echo json_encode($hasil);
		}else{
			$data = $this->input->post();
			$this->load->view('parameter/add',['data'=>$data]);
		}
	}
	/**
     * Fungsi edit parameter
     * @AclName Edit parameter
     */
	public function edit($id){
		$data = $this->mparameter->getById('tblparameter','parameter_key',$id);
        if(empty($data)){
            redirect('parameter');
        }
		if($this->input->server('REQUEST_METHOD') == 'POST' ){
			$data = $this->input->post();
			$data['parameter_key'] = $this->input->post('parameter_key');
			$cek = $this->_save($data);
			$status = $cek?"sukses":"gagal";
			$hasil = array(
		        'status' => $status
		    );
		    echo json_encode($hasil);
		}else{
			$this->load->view('parameter/edit',['data'=>$data]);
		}

	}
	/**
     * Fungsi delete parameter
     * @AclName Delete parameter
     */
	public function delete($id){
		$data = $this->mparameter->getById('tblparameter','parameter_key',$id);
		if(empty($data)){
			redirect('parameter');
		}
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$cek = $this->mparameter->delete($this->input->post('parameter_key'));
			$status = $cek?"sukses":"gagal";
			$hasil = array(
		        'status' => $status
		    );
		    echo json_encode($hasil);
		}else{
			$this->load->view('parameter/delete',['data'=>$data]);
		}

	}
	private function _save($data){
		$data = array_map("strtoupper", $data);
		return $this->mparameter->save($data);
	}
	/**
     * Fungsi untuk export excel
     * @AclName Export Excel rayon
     */
	public function export(){
		excel('excelparameter','tblparameter','parameter/excel');
	}

}