<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembimbingan_model extends CI_Model {
	
	private $_table1 = "pembimbingan";
	private $_table2 = "litmas";
	private $_table3 = "jenis_pembimbingan";

    public function __construct(){
		parent::__construct();
    }

	public function get($filters=null, $limit=null){
		$sql = "SELECT A.*, B.NAMA_LITMAS, C.NAMA_JENIS FROM " . $this->_table1 . " A ";
		$sql .= "LEFT JOIN " . $this->_table2 . " B ON A.LITMAS_ID = B.ID ";
		$sql .= "LEFT JOIN " . $this->_table3 . " C ON A.JENIS_PEMBIMBINGAN_ID = C.ID ";
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
		//var_dump($sql);
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