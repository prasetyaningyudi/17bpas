<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_model extends CI_Model {
	
	private $_table1 = "MENU";
	private $_table2 = "ROLE_MENU";
	private $_table3 = "ROLE";	

    public function __construct(){
		parent::__construct();
    }

	public function get($filters=null, $limit=null){
		$sql = "SELECT A.*, B.MENU_NAME BMENU_NAME FROM " . $this->_table1 ." A LEFT JOIN ". $this->_table1 ." B ";
		$sql .= " ON A.MENU_ID = B.ID";
		$sql .= " WHERE 1=1";
		if(isset($filters) and $filters != null){
			foreach ($filters as $filter) {
				$sql .= " AND " . $filter;
			}
		}
		$sql .= " ORDER BY MENU_ORDER ASC";
		if(isset($limit) and $limit != null){
			$sql .= " LIMIT ".$limit[0]." OFFSET ".$limit[1];
		}
		//var_dump($sql); die;
		
		$query = $this->db->query($sql);
		$result = $query->result();
		//var_dump($result); die;	
		return $result;
	}
	
	public function get_parent(){
		$sql = "SELECT * FROM " . $this->_table1;
		$sql .= " WHERE MENU_ID IS NULL";
		$sql .= " ORDER BY MENU_ORDER ASC";
		
		$query = $this->db->query($sql);
		$result = $query->result();
		return $result;
	}

	public function get_menu($role){
		$sql = "SELECT B.* FROM " . $this->_table2 . " A";
		$sql .= " LEFT JOIN " . $this->_table1 . " B ";
		$sql .= " ON A.MENU_ID = B.ID ";
		$sql .= " WHERE B.MENU_ID IS NULL AND B.STATUS = '1' AND A.ROLE_ID = '" . $role . "'";
		$sql .= " ORDER BY MENU_ORDER ASC";		
		$query = $this->db->query($sql);
		$result = $query->result();
		return $result;		
	}
	
	public function get_sub_menu($role){
		$sql = "SELECT B.* FROM " . $this->_table2 . " A";
		$sql .= " LEFT JOIN " . $this->_table1 . " B ";
		$sql .= " ON A.MENU_ID = B.ID ";
		$sql .= " WHERE B.MENU_ID IS NOT NULL AND B.STATUS = '1' AND A.ROLE_ID = '" . $role . "'";
		$sql .= " ORDER BY MENU_ORDER ASC";
		//var_dump($sql);die;		
		$query = $this->db->query($sql);
		$result = $query->result();
		return $result;		
	}

	public function get_guest_id($filter){
		$sql = "SELECT * FROM " . $this->_table3;
		$sql .= " WHERE ROLE_NAME = '".$filter."' ";
		//var_dump($sql);die;		
		$query = $this->db->query($sql);
		$result = $query->result();
		$id = 0;
		if (!empty($result)) {
			foreach($result as $value){
				$id = $value->ID;
			}
		}
		//var_dump($id);
		return $id;		
	}	
	
	public function insert($data){
		$result = $this->db->insert($this->_table1, $data);
		return $result;
	}
	
	public function update($data, $id){;
		$this->db->where('ID', $id);
		$result = $this->db->update($this->_table1, $data);
		return $result;
	}
	
	public function delete($data){
		$result = $this->db->delete($this->_table1, $data);
		return $result;
	}
	
}
