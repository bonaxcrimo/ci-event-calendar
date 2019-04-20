<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Extension extends MY_Controller {
    public function __construct(){
        parent::__construct();
        session_start();
        $this->load->model([
            'Macos',
            'Mcalendar'
        ]);
    }
    public function download($filename){
        $this->load->helper('download');
        $data = file_get_contents('uploads/'.$filename);
        force_download($filename,$data);
    }
    public function getRoles(){
        $role = $this->db->get('tblroles')->result();
        echo json_encode($role);
    }
    public function getEvents()
    {
        $result=$this->Mcalendar->getEvents();
        echo json_encode($result);
    }
    public function folder(){
        $this->load->helper('directory');
        $map = directory_map('./banklagu/', 1);
        $data['map'] = $map;
        $this->load->view('lagu/folder',$data);
    }
    public function acos(){
        $this->load->view('menu/acos');
    }
    public function ajax_list_acos(){
        $list = $this->Macos->get_datatables();
        $data= array();
        $no = $_POST['start'];

        foreach($list as $acos){
            $row = array();
            // $select="<input class='checkbox-".$acos->acosid."'  type='checkbox' name='role_permission[]' id='role_permission' value='".$acos->acosid."'>";
            $row[] =$acos->acosid;
            $row[] = $acos->class;
            $row[] = $acos->method;
            $row[] = $acos->displayname;
            $data[] = $row;
        }
        $output = array(
                        "draw" => @$_POST['draw'],
                        "recordsTotal" => $this->Macos->count_all(),
                        "recordsFiltered" => $this->Macos->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }
    public function ajax_list_roles(){
        $list = $this->Macos->get_datatables();
        $data= array();
        $no = $_POST['start'];
        foreach($list as $acos){
            $row = array();
            $row[] = $acos->acosid;
            $row[] = $acos->class;
            $row[] = $acos->method;
            $row[] = $acos->displayname;
            $data[] = $row;
        }
        $output = array(
                        "draw" => @$_POST['draw'],
                        "recordsTotal" => $this->Macos->count_all(),
                        "recordsFiltered" => $this->Macos->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }
}