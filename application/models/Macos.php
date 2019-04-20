<?php
class Macos extends MY_Model{
    protected $table = 'tblacos';
    protected $alias = 'tblacos';
    protected $column_order = [NULL,'class','method','displayname'];
    protected $column_search = [NULL,'class','method','displayname'];
    protected $order = array('acosid','asc');//default order
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
    function count($where){
        $sql = $this->db->query("SELECT * FROM tblacos " . $where);
        return $sql;
    }
    function get($where, $sidx, $sord, $limit, $start){
        if($sidx=="class"){
            $sidx .=",method ";
        }
        $query = "select * from tblacos " . $where." ORDER BY $sidx $sord LIMIT $start , $limit";
        return $this->db->query($query);
    }
    public function getList($conditions=[],$count=false,$limit=0,$offset=0){
        $table =$this->table;
        $alias = $this->alias;
        $this->db->from($table.' as '.$alias);
        $select = "";
        if(!empty($conditions)){
            $this->db->where($conditions);
        }
        if(!empty($limit)){
            $this->db->limit($limit,$offet);
        }
        if($count===true){
            return $this->db->get()->num_rows();
        }else{
            $this->db->order_by('class asc');
            return $this->db->get()->result();
        }
    }
    public function getGroup(){
        $table =$this->table;
        $this->db->from($table);
        $this->db->group_by('class');
        return $this->db->get()->result();
    }
    public function getByMultiId($ids){
        if(!empty($ids)){
            $acos = $this->db->where_in('acosid',$ids)
                    ->from($this->table)
                    ->get()
                    ->result_array();
            return $acos;
        }
        return [];

    }
    public function save($data){
        $conditions = [
            'class' => $data['class'],
            'method' =>$data['method']
        ];
        if($this->getBy($conditions,true) > 0){
            $result = $this->getBy($conditions);
            $id = $result[0]['acosid'];
            return $this->update($data,$id,'acosid');
        }else{
            return $this->insert($data);
        }
    }
}