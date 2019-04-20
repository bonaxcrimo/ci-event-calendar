<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mmenu extends MY_Model
{
    protected $table = 'tblmenu';
     protected $column_order = [null,null,'menuid','menuname','menuseq','menuparent','menuicon','acoid','link','modifiedby','modifiedon'];
    protected $column_search = [null,null,'menuid','menuname','menuseq','menuparent','menuicon','acoid','link','modifiedby','modifiedon'];
    protected $order = array('menuid','asc');//default order
    private function _get_datatables_query(){
        $this->db->select($this->table.".*,DATE_FORMAT(modifiedon,'%d-%m-%Y %T') modifiedonview ");
        $this->db->from($this->table);
        $i=0;
        foreach($this->column_search as $item){
            if(@$_POST['search']['value']){
                if($i===0){
                    $this->db->group_start();//open bracket
                    if($item!='')
                        $this->db->like($item,$_POST['search']['value']);
                }else{
                    if($item!='')
                        $this->db->or_like($item,$_POST['search']['value']);
                }
                if(count($this->column_search) - 1==$i)
                    $this->db->group_end();//close bracket
            }else if(@$_POST['columns'][$i]['search']['value']){
                if(trim($_POST['columns'][$i]['search']['value'])!=""){
                    if($item=="modifiedon")
                        $this->db->like("DATE_FORMAT(modifiedon,'%d-%m-%Y %T')",$_POST['columns'][$i]['search']['value'],'after');
                    else
                        $this->db->like($item,$_POST['columns'][$i]['search']['value']);
                }
            }
            $i++;
        }
        if(isset($_POST['order'])){
             $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }else if(isset($this->order)){
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
    function get_datatables()
    {
        $this->_get_datatables_query();
        if(@$_POST['length'] != -1)
            $this->db->limit(@$_POST['length'], @$_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    public function save($data) {
        $this->db->trans_start();
        $data['modifiedon'] =  date("Y-m-d H:i:s");
        $data['modifiedby'] = strtoupper($_SESSION[SESSION_NAME.'username']);
        if(trim(@$data['aconame'])==""){
            $data['acoid']=0;
        }
        if (isset($data['menuid']) && !empty($data['menuid'])) {
            $id = $data['menuid'];
            unset($data['menuid']);
            $save = $this->_preFormat($data); //format the fields

            $result = $this->update($save, $id,'menuid');
            if($result === true ){
            } else {
                $this->db->trans_rollback();
            }
        } else {
            $save = $this->_preFormat($data);//format untuk field
            $result = $this->insert($save);
            if($result === true){

            } else {
                $this->db->trans_rollback();
            }
        }
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
    public function delete($id){
        $this->db->where(['menuid'=>$id]);
        return $this->db->delete($this->table);
    }
    private function _preFormat($data){
        $fields = ['menuname','menuseq','menuparent','menuicon','acoid','modifiedon','modifiedby','link'];
        $save = [];
        foreach($fields as $val){
            if(isset($data[$val])){
                $save[$val] = $data[$val];
            }
        }
        return $save;
    }
    function count($where){
        $sql = $this->db->query("SELECT menuid FROM tblmenu " . $where);
        return $sql;
    }
    function get($where, $sidx, $sord, $limit, $start){
        $sort = " menuname asc ";
        if($sidx!="1"){
            $sort = " $sidx $sord ";
        }
        $sql = "SELECT *,
        DATE_FORMAT(modifiedon,'%d-%m-%Y %T') modifiedonview
        FROM tblmenu " . $where . " ORDER BY $sort , menuseq ASC LIMIT $start , $limit";
        return $this->db->query($sql);
    }

    function get_where($where){
        $sql = "SELECT menuid FROM tblmenu " . $where;
        return $this->db->query($sql);
    }




    function reseq(){
        $query = "SELECT DISTINCT(menuparent) FROM tblmenu ORDER BY menuid ASC";
        $sql = $this->db->query($query);
        foreach ($sql->result() as $key) {
            $query="SELECT menuid FROM tblmenu WHERE menuparent='$key->menuparent' ORDER BY menuseq ASC";
            $sql = $this->db->query($query);
            $i=0;
            foreach ($sql->result() as $key) {
                $i=$i+10;
                $query = "UPDATE tblmenu SET menuseq='$i' WHERE menuid='$key->menuid'";
                $this->db->query($query);
            }
        }
        return true;
    }


}