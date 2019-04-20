<?php
class Mroles extends MY_Model{
    protected $table = 'tblroles';
    protected $alias = 'r';
    protected $column_order = [null,null,'rolename','modifiedby','modifiedon'];
    protected $column_search = [null,null,'rolename','modifiedby','modifiedon'];
    protected $order = array('roleid','asc');//default order
    private function _get_datatables_query(){
        $this->db->select($this->table.".*,DATE_FORMAT(modifiedon,'%d-%m-%Y %T') modifiedonview ");
        $this->db->from($this->table);
        $this->db->where('rolename != "GUEST" and rolename!="SUPERADMIN"');
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
    function get_datatables(){
        $this->_get_datatables_query();
        if(@$_POST['length'] != -1)
            $this->db->limit(@$_POST['length'], @$_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered(){
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all(){
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    public function count($where){
        $sql = $this->db->query("SELECT * FROM tblroles " . $where);
        return $sql;
    }
    public function get($where, $sidx, $sord, $limit, $start){
        if(trim($where)==''){
            $cond = ' where rolename != "GUEST" ';
        }else{
            $cond = ' and rolename != "GUEST" ';
        }
        $sort = " rolename asc ";
        if($sidx!="1"){
            $sort = " $sidx $sord ";
        }
        $query = "select *,
        DATE_FORMAT(modifiedon,'%d-%m-%Y %T') modifiedonview from tblroles " . $where.$cond." ORDER BY $sort LIMIT $start , $limit";
        return $this->db->query($query);
    }
    public function getList($conditions=[],$count=false,$limit=0,$offset=0){
        $table= $this->table;
        $alias = $this->alias;
        $this->db->from($table.' as '.$alias);
        $select = "$alias.roleid, $alias.rolename , (select GROUP_CONCAT(tblacl.acoid SEPARATOR ',') from tblacl where tblacl.roleid= $alias.roleid) as acos";
        if(!empty($conditions)){
            $this->db->where($conditions);
        }
        if(!empty($limit)){
            $this->db->limit($limit,$offset);
        }
        if($count===true){
            return $this->db->get()->num_rows();
        }else{
            $this->db->select($select);
            return $this->db->get()->result();
        }
    }
    public function getByIdRoles($id){
        $conditions = [
            'roleid' =>$id
        ];
        $roles = $this->getList($conditions);
        if(!empty($roles)){
            $roles = $roles[0];
        }
        return $roles;
    }

    public function getGuestGroup(){
        $conditions= [
            'rolename' =>'Guest'
        ];
        $group = $this->getList($conditions);

        if(!empty($group)){
            $group = $group[0];
            $this->load->model('Macos');
            $ids = strpos($group->acos,',')===false?$group->acos:explode(', ',$group->acos);
            $group->acos = $this->Macos->getByMultiId($ids);

            // print_r($group);
            return $group;
        }
        return [];
    }
    public function isNameExists($name,$id=null){
        $conditions = [
            $this->alias.'.rolename'=>$name
        ];
        if(!empty($id) && is_numeric($id)){
            $conditions[$this->alias.'.roleid !='] = $id;
        }
        $count = $this->getList($conditions,true);
        if($count>0)
            return true;
        return false;
    }
    public function save($data){
        $this->db->trans_begin();
        $save=[
            'rolename'=>strtoupper($data['rolename']),
            'modifiedby'=>strtoupper($_SESSION[SESSION_NAME.'username'])
        ];
        if(isset($data['roleid']) && !empty($data['roleid'])){
            $id = $data['roleid'];
            $result = $this->update($save,$id,'roleid');
            // if($result===true && isset($data['role_permission'])){
            if($result===true && isset($data['role_permission'])){
                $this->saveRolePermission($id,$data['role_permission']);
            }else{
                $this->db->trans_rollback();
            }
        }else{
            $result = $this->insert($save);
            if($result===true && isset($data['role_permission'])){
                $this->saveRolePermission($this->db->insert_id(),$data['role_permission']);
            }else{
                $this->db->trans_rollback();
            }
        }
        if($this->db->trans_status() === false){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return true;
        }
    }
    public function saveRolePermission($roleid,$acos){
        $this->load->model('Macl');
        $result = $this->Macl->saveBatch(['roleid'=>$roleid,'acos'=>$acos]);
        return $result;
    }
    public function delete($id){
        $this->load->model('Muserroles');
        $this->Muserroles->deleteByRole($id);
        $this->load->model('Macl');
        $this->Macl->deleteByRole($id);
        $this->db->where(['roleid'=>$id]);
        $this->db->delete($this->table);
        return true;
    }
}
