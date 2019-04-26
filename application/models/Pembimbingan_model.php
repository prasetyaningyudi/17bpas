<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembimbingan_model extends CI_Model {
	
	private $_table1 = "pembimbingan";
	private $_table2 = "litmas";
	private $_table3 = "jenis_pembimbingan";
	private $_table4 = "user";

    public function __construct(){
		parent::__construct();
    }

	public function get($filters=null, $limit=null){
		$sql = "SELECT A.*, B.NAMA_LITMAS, C.NAMA_JENIS, D.USERNAME FROM " . $this->_table1 . " A ";
		$sql .= "LEFT JOIN " . $this->_table2 . " B ON A.LITMAS_ID = B.ID ";
		$sql .= "LEFT JOIN " . $this->_table3 . " C ON A.JENIS_PEMBIMBINGAN_ID = C.ID ";
		$sql .= "LEFT JOIN " . $this->_table4 . " D ON A.USER_ID = D.ID ";
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
	
	public function get_rekap($filters=null, $limit=null){
		$sql = "SELECT B.NAMA_LITMAS, COUNT(A.LITMAS_ID) JUMLAH FROM " . $this->_table1 . " A ";
		$sql .= "RIGHT JOIN " . $this->_table2 . " B ON A.LITMAS_ID = B.ID ";
		$sql .= " WHERE 1=1";
		if(isset($filters) and $filters != null){
			foreach ($filters as $filter) {
				$sql .= " AND " . $filter;
			}
		}
		$sql .= " GROUP BY A.LITMAS_ID";		
		$sql .= " ORDER BY B.ID ASC";
		if(isset($limit) and $limit != null){
			$sql .= " LIMIT ".$limit[0]." OFFSET ".$limit[1];
		}
		//var_dump($sql);die;
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