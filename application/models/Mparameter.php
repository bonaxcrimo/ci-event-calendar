<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mparameter extends MY_Model {
	protected $table = 'tblparameter';
    protected $column_order = [null,null,'parametergrpid','parameterid','parametertext','parametermemo','modifiedby','modifiedon'];
    protected $column_search = [null,null,'parametergrpid','parameterid','parametertext','parametermemo','modifiedby','modifiedon'];
    protected $order = array('parameter_key','asc');//default order
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
        if (isset($data['parameter_key']) && !empty($data['parameter_key'])) {
            $id = $data['parameter_key'];
            unset($data['parameter_key']);
            $save = $this->_preFormat($data); //format the fields

            $result = $this->update($save, $id,'parameter_key');
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
        $this->db->where(['parameter_key'=>$id]);
        return $this->db->delete($this->table);
    }
	private function _preFormat($data){
    	$fields = ['parametergrpid','parameterid','parametertext','parametermemo','modifiedon','modifiedby'];
    	$save = [];
    	foreach($fields as $val){
    		if(isset($data[$val])){
    			$save[$val] = $data[$val];
    		}
    	}
    	return $save;
    }
	function count($where){
		$sql = "SELECT parameter_key FROM tblparameter " . $where;
        return $this->db->query($sql);
	}
	function get($where, $sidx, $sord, $limit, $start){
        $sort = " parametergrpid asc,parameterid asc ";
        if($sidx!="1"){
            $sort = " $sidx $sord ";
        }
		$sql = "SELECT *,
		DATE_FORMAT(modifiedon,'%d-%m-%Y %T') modifiedonview
		FROM tblparameter " . $where . " ORDER BY $sort  LIMIT $start , $limit";
		return $this->db->query($sql);
	}

}