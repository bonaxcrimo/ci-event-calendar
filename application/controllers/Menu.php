<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu extends MY_Controller {

    public function __construct(){
        parent::__construct();
        session_start();
        $this->load->model('mmenu');
    }
    /**
     * Fungsi menu menu
     * @AclName menu Menu
     */
    public function index(){
        $this->render('menu/grid');
    }
    public function ajax_list(){
        $list = $this->mmenu->get_datatables();
        $data= array();
        $no = $_POST['start'];
        foreach($list as $menu){
            $no++;
            $row = array();
            $view = "<button class='btn btn-primary btn-sm' onclick=\"view('".$menu->menuid."')\" ".(hasPermission('menu','view')?'':'disabled')."><i class='fa fa-eye'></i></button> ";
            $edit = " <button class='btn btn-warning btn-sm' onclick=\"edit('".$menu->menuid."')\" ".(hasPermission('menu','edit')?'':'disabled')."><i class='fa fa-edit'></i></button> ";
            $del = " <button class='btn btn-danger btn-sm' onclick=\"deleted('".$menu->menuid."')\" ".(hasPermission('menu','delete')?'':'disabled')."><i class='fa fa-trash'></i></button> ";
            $aksi = $view.$edit.$del;
            $route = count(getTableWhere('tblacos',["acosid"=>$menu->acoid]))>0?getTableWhere('tblacos',["acosid"=>$menu->acoid])[0]:'-';
            $menu->acoid = $route!='-'?$route->class."/".$route->method:$route;
            $row[] = $no;
            $row[] = $aksi;
            $row[] = $menu->menuid;
            $row[] = $menu->menuname;
            $row[] = $menu->menuseq;
            $row[] = $menu->menuparent;
            $row[] = $menu->menuicon;
            $row[] = $menu->acoid;
            $row[] = $menu->link;
            $row[] = $menu->modifiedby;
            $row[] = $menu->modifiedonview;
            $data[] = $row;
        }
        $output = array(
                        "draw" => @$_POST['draw'],
                        "recordsTotal" => $this->mmenu->count_all(),
                        "recordsFiltered" => $this->mmenu->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }
    /**
     * Fungsi view Menu
     * @AclName View Menu
     */
    public function view($id){
        $data = $this->mmenu->getById('tblmenu','menuid',$id);
        if(empty($data)){
            redirect('menu');
        }
        $detail = $this->mmenu->getById('tblacos','acosid',$data->acoid);
        $this->load->view('menu/view',['data'=>$data,'detail'=>$detail]);
    }
    /**
     * Fungsi add Menu
     * @AclName Tambah Menu
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
            $this->load->view('menu/form',['data'=>$data]);
        }
    }
    /**
     * Fungsi edit menu
     * @AclName Edit Menu
     */
    public function edit($id){
        $data = $this->mmenu->getById('tblmenu','menuid',$id);
        if(empty($data)){
            redirect('menu');
        }
        $detail = $this->mmenu->getById('tblacos','acosid',$data->acoid);
        if($this->input->server('REQUEST_METHOD') == 'POST' ){
            $data = $this->input->post();
            $data['menuid'] = $this->input->post('menuid');
            $cek = $this->_save($data);
            $status = $cek?"sukses":"gagal";
            $hasil = array(
                'status' => $status
            );
            echo json_encode($hasil);
        }else{
            $this->load->view('menu/form',['data'=>$data,'detail'=>$detail]);
        }

    }
    /**
     * Fungsi delete Menu
     * @AclName Delete Menu
     */
    public function delete($id){
        $data = $this->mmenu->getById('tblmenu','menuid',$id);
        if(empty($data)){
            redirect('menu');
        }
        $detail = $this->mmenu->getById('tblacos','acosid',$data->acoid);
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $cek = $this->mmenu->delete($this->input->post('menuid'));
            $status = $cek?"sukses":"gagal";
            $hasil = array(
                'status' => $status
            );
            echo json_encode($hasil);
        }else{
            $this->load->view('menu/delete',['data'=>$data,'detail'=>$detail]);
        }

    }
    private function _save($data){
        return $this->mmenu->save($data);
    }



    public function export(){
    }
    /**
     * Fungsi re sequence
     * @AclName Re sequence
     */
    public function reseq(){
        return $this->mmenu->reseq();
    }

}