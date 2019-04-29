<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
	
	private $_table1 = "USER";
	private $_table2 = "ROLE";
	private $_table3 = "USER_INFO";

    public function __construct(){
		parent::__construct();
    }

	public function get($filters=null, $limit=null){
		$sql = "SELECT A.*, B.ID BID, B.ROLE_NAME, C.PHOTO_1 FROM " . $this->_table1 . " A ";
		$sql .= " LEFT JOIN " . $this->_table2 . "  B";
		$sql .= " ON A.ROLE_ID = B.ID";		
		$sql .= " LEFT JOIN " . $this->_table3 . "  C";
		$sql .= " ON A.ID = C.USER_ID";
		$sql .= " WHERE 1=1";
		if(isset($filters) and $filters != null){
			foreach ($filters as $filter) {
				$sql .= " AND " . $filter;
			}
		}
		$sql .= " ORDER BY A.ID DESC";
		if(isset($limit) and $limit != null){
			$sql .= " LIMIT ".$limit[0]." OFFSET ".$limit[1];
		}
		//var_dump($sql); die;
		$query = $this->db->query($sql);
		$result = $query->result();
		return $result;
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