<?php
class Museracl extends MY_Model{
    protected $table = 'tbluseracl';
    protected $alias = 'ua';
    protected $column_order = [null,null,'acoid','modifiedby','modifiedon'];
    protected $column_search =[null,null,'acoid','modifiedby','modifiedon'];
    protected $order = array('useraclid','asc');//default order
    private function _get_datatables_query($userpk){
        $table = $this->table;
        $alias = $this->alias;
        $this->db->from($table);
        $this->db->where('userpk',$userpk);
        $select = "*,DATE_FORMAT(modifiedon,'%d-%m-%Y %T') modifiedonview";
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
    function get_datatables($userpk)
    {
        $this->_get_datatables_query($userpk);
        if(@$_POST['length'] != -1)
            $this->db->limit(@$_POST['length'], @$_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($userpk)
    {
        $this->_get_datatables_query($userpk);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($userpk)
    {
        $this->db->where('userpk',$userpk);
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    public function count($where){
        $sql = $this->db->query("SELECT * FROM tbluseracl " . $where);
        return $sql;
    }
    public function getList($conditions=[],$count=false,$limit=0,$offset=0){
        $table= "tbluser";
        $alias = "u";
        $this->db->from($table.' as '.$alias);
        $select = "$alias.userpk, (select GROUP_CONCAT(tbluseracl.acoid SEPARATOR ',') from tbluseracl where tbluseracl.userpk= $alias.userpk) as acos";
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
    public function getByUserPK($userpk){
       if(empty($userpk)){
            return [];
        }
        $alias = $this->alias;
        $acl  = $this->db->distinct('acos.class,acos.method,acos.display_name')
                    ->where_in('userpk',$userpk)
                    ->from($this->table.' as '.$this->alias)
                    ->join('tblacos','tblacos.acosid = '.$this->alias.'.acoid','inner')
                    ->get()
                    ->result_array();

        return $acl;
    }
    public function getByIdUser($id){
        $conditions = [
            'userpk' =>$id
        ];
        $data = $this->getList($conditions);
        if(!empty($data)){
            $data = $data[0];
        }
        return $data;
    }
    public function save($data){
        $this->db->trans_start();
        if(isset($data['userpk']) && !empty($data['userpk'])){
            $id = $data['userpk'];
            $this->saveRolePermission($id,$data['role_permission']);
        }
        if($this->db->trans_status() === false){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return true;
        }
    }
    public function saveRolePermission($userpk,$acos){
        $result = $this->saveBatch(['userpk'=>$userpk,'acos'=>$acos]);
        return $result;
    }
    public function saveBatch($data){
        $insert= [];
        if(isset($data['acos'])){
            $records = $this->getListAll('tbluseracl',['userpk'=>$data['userpk']],true);
            if(empty($records)){
                foreach($data['acos'] as $aco){
                    $insert[] = [
                        'userpk'=>$data['userpk'],
                        'acoid'=>$aco,
                        'modifiedby'=>strtoupper($_SESSION[SESSION_NAME.'username']),
                        'modifiedon'=>date("Y-m-d H:i:s")
                    ];
                }
                return $this->insertBatch($insert);
            }else{
                $records = $this->getListByGroup(['userpk'=>$data['userpk']]);
                $acos = strpos($records[0]['acos'],',')===false?[$records[0]['acos']]:explode(', ',$records[0]['acos']);
                $inserts = array_diff($data['acos'],$acos);
                $removes = array_diff($acos,$data['acos']);
                if(!empty($inserts)){
                    $insert= [];
                    foreach($inserts as $val){
                        $insert[]=[
                            'userpk'=>$data['userpk'],
                            'acoid'=>$val,
                            'modifiedby'=>strtoupper($_SESSION[SESSION_NAME.'username']),
                            'modifiedon'=>date("Y-m-d H:i:s")
                        ];
                    }
                    $this->insertBatch($insert);
                }
                if(!empty($removes)){
                    $remove =[];
                    foreach($removes as $val){
                        $remove[] = [
                            'userpk' =>$data['userpk'],
                            'acoid'=>$val
                        ];
                    }
                    $this->removeBatch($remove);
                }
                return true;
            }
        }
        return false;
    }
    protected function getListByGroup($conditions){
        $this->db->select('GROUP_CONCAT('.$this->alias.'.acoid SEPARATOR "," ) as acos ')
            ->where($conditions)
            ->from($this->table.' as '.$this->alias);

        return $this->db->get()->result_array();
    }
    public function get($where, $sidx, $sord, $limit, $start){
        $query = "select * ,DATE_FORMAT(modifiedon,'%d-%m-%Y %T') modifiedonview from tbluseracl " . $where." ORDER BY $sidx $sord LIMIT $start , $limit";
        return $this->db->query($query);
    }
}