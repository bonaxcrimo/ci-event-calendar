<?php
Class Muser extends MY_Model{
    protected $table = 'tbluser';
    protected $alias = 'u';
    protected $insert_id;
    protected $column_order = [null,null,'userid','username','dashboard','rolename','modifiedby','modifiedon'];
    protected $column_search =[null,null,'userid','username','dashboard','rolename','u.modifiedby','u.modifiedon'];
    protected $order = array('userpk','asc');//default order
    private function _get_datatables_query(){
        $table = $this->table;
        $alias = $this->alias;
        $this->db->from($table.' as '.$alias);
        $this->db->join('tbluserroles', 'tbluserroles.userpk = u.userpk', 'left');
        $this->db->join('tblroles', 'tblroles.roleid = tbluserroles.roleid', 'left');
        $select = "$alias.userpk, $alias.username,DATE_FORMAT($alias.modifiedon,'%d-%m-%Y %T') modifiedonview,$alias.modifiedby, $alias.userid, $alias.password,$alias.dashboard, (select group_concat(ur.roleid separator ',') from tbluserroles ur where ur.userpk =  $alias.userpk) as roles,(select group_concat(r.rolename separator ',') from tbluserroles ur inner join tblroles r ON r.roleid = ur.roleid where ur.userpk = $alias.userpk)as roles_name";
        $this->db->select($select);
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
                        $this->db->like("DATE_FORMAT($alias.modifiedon,'%d-%m-%Y %T')",$_POST['columns'][$i]['search']['value'],'after');
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
    public function getList($conditions=[],$count=false,$limit=0,$offset=0){
        $table = $this->table;
        $alias = $this->alias;
        $this->db->from($table.' as '.$alias);
        $select = "$alias.userpk, $alias.username, $alias.userid, $alias.password,$alias.dashboard, (select group_concat(ur.roleid separator ',') from tbluserroles ur where ur.userpk =  $alias.userpk) as roles,(select group_concat(r.rolename separator ',') from tbluserroles ur inner join tblroles r ON r.roleid = ur.roleid where ur.userpk = $alias.userpk)as roles_name";
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
    public function getByIdUser($id){
        $conditions = ['userpk'=>$id];
        $user = $this->getList($conditions);
        if(!empty($user)){
            $user = $user[0];
            return $user;
        }
        return [];
    }
    public function delete($id){
        $this->db->delete('tbluserroles',['userpk'=>$id]);
        $this->db->where(['userpk'=>$id]);
        return $this->db->delete($this->table);
    }
    public function save($data) {
        $this->db->trans_start();
        $data['modifiedon'] =  date("Y-m-d H:i:s");
        $data['modifiedby'] = strtoupper($_SESSION[SESSION_NAME.'username']);
        //encypt the password
        if(isset($data['password']) && !empty($data['password'])){
            $data['password'] = $this->_hashPassword($data['password']);
        }
        $data['userid']=strtoupper($data['userid']);
        $data['username']=strtoupper($data['username']);
        if (isset($data['userpk']) && !empty($data['userpk'])) {
            $id = $data['userpk'];
            unset($data['userpk']);
            $save = $this->_preFormat($data); //format the fields

            $result = $this->update($save, $id,'userpk');
            if($result === true ){

                if(isset($data['user_roles'])){
                    $this->saveUserRoles($id, $data['user_roles']);
                }
            } else {
                $this->db->trans_rollback();
            }
        } else {
            $save = $this->_preFormat($data);//format untuk field
            $result = $this->insert($save);
            if($result === true){
                $id = $this->insert_id;
                if(isset($data['user_roles'])){
                    $this->saveUserRoles($id, $data['user_roles']);
                }

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
    public function saveUserRoles($userpk,$roles){
        $this->load->model('Muserroles');
        $result = $this->Muserroles->saveBatch(['userpk'=>$userpk,'roles'=>$roles]);
        return $result;
    }
    private function _preFormat($data){
        $fields = ['userid','username','password','dashboard','modifiedon','modifiedby'];
        $save = [];
        foreach($fields as $val){
            if(isset($data[$val])){
                $save[$val] = $data[$val];
            }
        }
        return $save;
    }
    public function _hashPassword($password){
        return md5($password);
    }
    function count($where){
        $sql = "SELECT tbluser.userpk FROM tbluser left join tbluserroles ur on ur.userpk = tbluser.userpk left join tblroles r on r.roleid=ur.roleid  " . $where." group by tbluser.userpk";
        return $this->db->query($sql);
    }
    function get($where, $sidx, $sord, $limit, $start){
        $sort = " userid asc ";
        if($sidx!="1"){
            $sort = " $sidx $sord ";
        }
        $sql = "SELECT tbluser.*,
        DATE_FORMAT(tbluser.modifiedon,'%d-%m-%Y %T') modifiedonview
        FROM tbluser left join tbluserroles ur on ur.userpk = tbluser.userpk left join tblroles r on r.roleid=ur.roleid " . $where . " group by tbluser.userpk ORDER BY $sort LIMIT $start , $limit";
        return $this->db->query($sql);
    }
    function add($tabel,$data){
        $this->db->insert($tabel,$data);
        $userpk="";
        $query = "SELECT userpk FROM tbluser ORDER BY userpk DESC LIMIT 0,1";
        $sql = $this->db->query($query);
        foreach ($sql->result() as $key) {
            $userpk .= $key->userpk;
        }
        return $userpk;
    }
    function edit($tabel,$data,$id){
        $query = $this->db->where("userpk",$id);
        $query = $this->db->update($tabel,$data);
    }
    function getwhere($userpk){
        $sql = "SELECT *,
        DATE_FORMAT(modifiedon,'%d-%m-%Y %T') modifiedon
        FROM tbluser WHERE userpk ='$userpk' LIMIT 0,1";
        return $this->db->query($sql);
    }
    function del($tabel,$id){
        $query = $this->db->where("userpk",$id);
        $sql = $this->db->delete($tabel);
        return $sql;
    }
}
?>
