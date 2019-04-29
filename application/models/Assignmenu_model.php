<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Assignmenu_model extends CI_Model {
	
	private $_table1 = "MENU";
	private $_table2 = "ROLE";
	private $_table3 = "ROLE_MENU";

    public function __construct(){
		parent::__construct();
    }

	public function get($filters=null, $limit=null){
		$sql = "SELECT A.*, B.MENU_NAME, C.ROLE_NAME FROM " . $this->_table3 ." A LEFT JOIN ". $this->_table1 ." B ";
		$sql .= " ON A.MENU_ID = B.ID";
		$sql .= " LEFT JOIN ". $this->_table2 ." C ";		
		$sql .= " ON A.ROLE_ID = C.ID";
		$sql .= " WHERE 1=1";
		if(isset($filters) and $filters != null){
			foreach ($filters as $filter) {
				$sql .= " AND " . $filter;
			}
		}
		$sql .= " ORDER BY C.ROLE_NAME ASC, B.MENU_ORDER";
		if(isset($limit) and $limit != null){
			$sql .= " LIMIT ".$limit[0]." OFFSET ".$limit[1];
		}
		//var_dump($sql); die;
		
		$query = $this->db->query($sql);
		$result = $query->result();
		//var_dump($result); die;	
		return $result;
	}
	
	public function insert($data){
		$result = $this->db->insert($this->_table3, $data);
		return $result;
	}
	
	public function update($data, $id){;
		$this->db->where('ID', $id);
		$result = $this->db->update($this->_table3, $data);
		return $result;
	}
	
	public function delete($data){
		$result = $this->db->delete($this->_table3, $data);
		return $result;
	}
	
}
