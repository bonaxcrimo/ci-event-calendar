<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mmenutop extends MY_Model
{
	public function get_data($induk=0)
	{
		$userpk = $_SESSION[SESSION_NAME.'userpk'];
		$data = array();
		$query="SELECT  t1.menuid,t1.menuname,t1.menuicon,t3.class,t3.method,t1.link,t1.menuparent
			FROM tblmenu t1 left join tblacos t3 on t1.acoid = t3.acosid
						WHERE t1.menuparent='$induk' and (t1.acoid = t3.acosid or t1.menuparent=0 or t1.link!='')   ORDER BY menuseq ASC";
		$result = $this->db->query($query);

		foreach($result->result() as $row)
		{
			if($row->menuparent==0 and count($this->get_data($row->menuid))==0 and ($row->menuid!=1) and $row->menuid!=10){
				continue;
			}else if(strtolower($row->menuname)=="report design" and strtolower(strtoupper($_SESSION[SESSION_NAME.'username']))!='admin'){
				continue;
			}else{
				if(hasPermission($row->class,$row->method)){
					$data[] = array(
							'menuid'	=>$row->menuid,
							'menuname'	=>$row->menuname,
							'menuicon'	=>$row->menuicon,
							'menuparent'=>$row->menuparent,
							'link' => $row->link,
							'menuexe'	=>$row->class."/".$row->method,
							// recursive
							'child'	=>$this->get_data($row->menuid)
						);
				}
			}
		}

		return $data;
	}
	public function get_child($menuid)
	{
		$data = array();
		$this->db->from('tblmenu');
		$this->db->where('menuparent',$menuid);
		$this->db->order_by("menuseq", "asc");
		$result = $this->db->get();
		foreach($result->result() as $row)
		{
			$data[$row->menuid] = $row->menuname;
		}
		return $data;
	}

	function get_menuid($menuexe){
		$userpk = $_SESSION[SESSION_NAME.'userpk'];
		$query = "SELECT t2.acl
			FROM tblmenu t1, tblusermenu t2
			WHERE t1.menuexe='$menuexe' AND t1.menuid=t2.menuid AND t2.userpk='$userpk'";
		$result = $this->db->query($query);
		foreach($result->result() as $row)
		{
			$data = $row->acl;
		}
		return @$data;
	}
}