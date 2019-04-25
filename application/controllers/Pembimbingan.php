<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembimbingan extends CI_Controller {
	private $data;
	
	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->library('auth');			
		$this->load->helper('url');			
		$this->load->database();
		$this->load->model('litmas_model');
		$this->load->model('jenispembimbingan_model');
		$this->load->model('pembimbingan_model');
		$this->load->model('menu_model');
		if(isset($this->session->userdata['is_logged_in'])){
			$this->data['menu'] = $this->menu_model->get_menu($this->session->userdata('ROLE_ID'));
			$this->data['sub_menu'] = $this->menu_model->get_sub_menu($this->session->userdata('ROLE_ID'));
		}else{
			$this->data['menu'] = $this->menu_model->get_menu($this->menu_model->get_guest_id('guest'));
			$this->data['sub_menu'] = $this->menu_model->get_sub_menu($this->menu_model->get_guest_id('guest'));			
		}
		$this->load->model('app_data_model');		
		$this->data['app_data'] = $this->app_data_model->get();			
		$this->data['error'] = array();
		$this->data['title'] = 'Pembimbingan';
		//var_dump($this->data['menu']);
	}

	public function index(){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}		
		$this->data['subtitle'] = 'Daftar';
		$this->data['class'] = __CLASS__;
		$this->load->view('section_header', $this->data);
		$this->load->view('section_sidebar');
		$this->load->view('section_nav');
		$this->load->view('main_index');	
		$this->load->view('section_footer');			
	}

	public function lists(){
		date_default_timezone_set("Asia/Jakarta");
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}		
		$filters = array();
		$limit = array('20', '0');
		$r_no = '';
		$r_tgl = '';
		$r_nama = '';
		$r_litmas = '';
		$r_berkas = '';
		$r_jenis = '';

		//var_dump($_POST['nama']);
		if(isset($_POST['submit'])){
			if (isset($_POST['no'])) {
				if ($_POST['no'] != '' or $_POST['no'] != null) {
					$filters[] = "NO_REGISTER LIKE '%" . $_POST['no'] . "%'";
					$r_no = $_POST['no'];
				}
			}
			if (isset($_POST['tgl'])) {
				if ($_POST['tgl'] != '' or $_POST['tgl'] != null) {
					$filters[] = "TGL_REGISTER = '" . $_POST['tgl'] . "'";
					$r_tgl = $_POST['tgl'];
				}
			}			
			if (isset($_POST['nama'])) {
				if ($_POST['nama'] != '' or $_POST['nama'] != null) {
					$filters[] = "NAMA LIKE '%" . $_POST['nama'] . "%'";
					$r_nama = $_POST['nama'];
				}
			}			
			if (isset($_POST['litmas'])) {
				if ($_POST['litmas'] != '' or $_POST['litmas'] != null) {
					$filters[] = "LITMAS_ID = '" . $_POST['litmas'] . "'";
					$r_litmas = $_POST['litmas'];
				}
			}
			if (isset($_POST['berkas'])) {
				if ($_POST['berkas'] != '' or $_POST['berkas'] != null) {
					$filters[] = "KET_BERKAS LIKE '%" . $_POST['berkas'] . "%'";
					$r_berkas = $_POST['berkas'];
				}
			}
			if (isset($_POST['jenis'])) {
				if ($_POST['jenis'] != '' or $_POST['jenis'] != null) {
					$filters[] = "JENIS_PEMBIMBINGAN_ID = '" . $_POST['jenis'] . "'";
					$r_jenis = $_POST['jenis'];
				}
			}			
			if (isset($_POST['offset'])) {
				if ($_POST['offset'] != '' or $_POST['offset'] != null) {
					$limit[1] = $_POST['offset'];
				}
			}			
		}
		
		$data = $this->pembimbingan_model->get($filters, $limit);
		//var_dump($data);
		$total_data = count($this->pembimbingan_model->get($filters));
		$limit[] = $total_data;
		
		//var_dump($data);
		$now = new DateTime(date('Y-m-d'));
		$no_body = 0;
		$body= array();
		if(isset($data)){
            if (empty($data)) {
                $body[$no_body] = array(
                    (object) array ('colspan' => 100, 'classes' => ' empty bold align-center', 'value' => 'No Data')
                );
			} else {
				foreach ($data as $value) {
					$body[$no_body] = array(
						(object) array( 'classes' => ' hidden ', 'value' => $value->ID ),
						(object) array( 'classes' => ' bold align-center ', 'value' => $limit[1] + ($no_body+1) ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->NO_REGISTER ),
						(object) array( 'classes' => ' align-left ', 'value' => date("d M Y", strtotime($value->TGL_REGISTER)) ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->NAMA ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->KASUS ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->NAMA_LITMAS ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->NAMA_JENIS ),
						(object) array( 'classes' => ' align-left ', 'value' => $now->diff(new DateTime($value->TGL_REGISTER))->format('%a hari') ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->KET_BERKAS ),
					);
					$no_body++;
				}
			}
        } else {
            $body[$no_body] = array(
                (object) array ('colspan' => 100, 'classes' => ' empty bold align-center', 'value' => 'Filter First')
            );
        }
		
		$header = array(
			array (
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'No'),
				(object) array ('colspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'no register'),								
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'tgl register'),			
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'nama'),			
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'kasus'),			
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'litmas'),		
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'jenis'),	
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'lama'),	
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'berkas'),	
			)		
		);

		$litmas = array();
		$filter = array();
		$filter[] = " STATUS = '1' "; 
		$data = $this->litmas_model->get($filter);
		if (empty($data)) {

		} else {
			foreach ($data as $value) {
				$litmas[] = (object) array('label'=>$value->NAMA_LITMAS, 'value'=>$value->ID);
			}
		}

		$jenis = array();
		$data = $this->jenispembimbingan_model->get();
		if (empty($data)) {

		} else {
			foreach ($data as $value) {
				$jenis[] = (object) array('label'=>$value->NAMA_JENIS, 'value'=>$value->ID);
			}
		}			
		
		$berkas = array();	
		$berkas[] = (object) array('label'=>'lengkap', 'value'=>'lengkap');
		$berkas[] = (object) array('label'=>'tidak lengkap', 'value'=>'tidak lengkap');
		
		$fields = array();
		$fields[] = (object) array(
			'type' 			=> 'text',
			'label' 		=> 'no register',
			'name' 			=> 'no',
			'placeholder'	=> 'no register',
			'value' 		=> $r_no,
			'classes' 		=> 'full-width',
		);	
		$fields[] = (object) array(
			'type' 			=> 'date',
			'label' 		=> 'tgl register',
			'name' 			=> 'tgl',
			'placeholder'	=> 'tgl register',
			'format'		=> 'YYYY-MM-DD',
			'value' 		=> $r_tgl,
			'classes' 		=> 'full-width',
		);
		$fields[] = (object) array(
			'type' 			=> 'text',
			'label' 		=> 'nama',
			'name' 			=> 'nama',
			'placeholder'	=> 'nama',
			'value' 		=> $r_nama,
			'classes' 		=> 'full-width',
		);			
		$fields[] = (object) array(
			'type' 			=> 'select',
			'label' 		=> 'litmas',
			'name' 			=> 'litmas',
			'placeholder'	=> '--pilih litmas--',
			'options'		=>  $litmas,
			'value' 		=> $r_litmas,
			'classes' 		=> 'full-width',
		);							
		$fields[] = (object) array(
			'type' 			=> 'select',
			'label' 		=> 'kelengkapan berkas',
			'name' 			=> 'berkas',
			'placeholder'	=> '--pilih kelengkapan berkas--',
			'options'		=>  $berkas,
			'value' 		=> $r_berkas,
			'classes' 		=> 'full-width',
		);		
		$fields[] = (object) array(
			'type' 			=> 'select',
			'label' 		=> 'jenis pembimbingan',
			'name' 			=> 'jenis',
			'placeholder'	=> '--pilih jenis pembimbingan--',
			'options'		=>  $jenis,
			'value' 		=> $r_jenis,
			'classes' 		=> 'full-width',
		);	

		$this->data['list'] = (object) array (
			'type'  	=> 'table_default',
			'data'		=> (object) array (
				'classes'  	=> 'striped bordered hover',
				'insertable'=> true,
				'editable'	=> true,
				'deletable'	=> true,
				'statusable'=> false,
				'detailable'=> true,
				'pdf'		=> false,
				'xls'		=> true,
				'title'		=> 'Daftar Pembimbingan',
				'pagination'=> $limit,
				'filters'  	=> $fields,
				'toolbars'	=> null,
				'header'  	=> $header,
				'body'  	=> $body,
				'footer'  	=> null,
			)
		);	
		
		if((isset($_POST['expected_output']))){
			if($_POST['expected_output'] == 'pdf'){
				$parameter = array (
					$this->data['list'], null, null, null, null, false, true, 10
				);				
				$this->load->library('generatepdf', $parameter);
				$this->generatepdf->generate_pdf('L', 'F4');
			}else if ($_POST['expected_output'] == 'xls') {
				$parameter = array (
					$this->data['list']
				);				
				$this->load->library('generatexls', $parameter);
				$this->generatexls->generate_xls();
			}
		}else{
			echo json_encode($this->data['list']);
		}
	}
	
	public function insert(){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}		
		if(isset($_POST['submit'])){
			$error_info = array();
			$error_status = false;
			if($_POST['no'] == ''){
				$error_info[] = 'no register tidak boleh kosong';
				$error_status = true;
			}
			if($_POST['tgl'] == ''){
				$error_info[] = 'tgl register tidak boleh kosong';
				$error_status = true;
			}
			if($_POST['nama'] == ''){
				$error_info[] = 'nama tidak boleh kosong';
				$error_status = true;
			}
			if($_POST['kasus'] == ''){
				$error_info[] = 'kasus tidak boleh kosong';
				$error_status = true;
			}
			if($_POST['berkas'] == ''){
				$error_info[] = 'keterangan berkas tidak boleh kosong';
				$error_status = true;
			}
			if($_POST['litmas'] == ''){
				$error_info[] = 'litmas tidak boleh kosong';
				$error_status = true;
			}
			if($_POST['jenis'] == ''){
				$error_info[] = 'jenis pembimbingan tidak boleh kosong';
				$error_status = true;
			}
		
			if($error_status == true){
				$this->data['error'] = (object) array (
					'type'  	=> 'error',
					'data'		=> (object) array (
						'info'	=> $error_info,
					)
				);				
				echo json_encode($this->data['error']);
			}else{
				$this->data['insert'] = array(
					'NO_REGISTER' => $_POST['no'],
					'TGL_REGISTER' => $_POST['tgl'],
					'NAMA' => $_POST['nama'],
					'KASUS' => $_POST['kasus'],
					'KET_BERKAS' => $_POST['berkas'],
					'LITMAS_ID' => $_POST['litmas'],
					'JENIS_PEMBIMBINGAN_ID' => $_POST['jenis'],
					'USER_ID' => $this->session->userdata('ID'),
				);	
				//var_dump($this->data['insert']);die;
				$result = $this->pembimbingan_model->insert($this->data['insert']);
				$info = array();
				$info[] = 'Insert data success';
				$this->data['success'] = (object) array (
					'type'  	=> 'success',
					'data'		=> (object) array (
						'info'	=> $info,
					)
				);			
				echo json_encode($this->data['success']);				
			}
		}else{
			$litmas = array();
			$filter = array();
			$filter[] = " STATUS = '1' "; 
			$data = $this->litmas_model->get($filter);
			
			if (empty($data)) {

			} else {
				foreach ($data as $value) {
					$litmas[] = (object) array('label'=>$value->NAMA_LITMAS, 'value'=>$value->ID);
				}
			}

			$jenis = array();
			$data = $this->jenispembimbingan_model->get();
			
			if (empty($data)) {

			} else {
				foreach ($data as $value) {
					$jenis[] = (object) array('label'=>$value->NAMA_JENIS, 'value'=>$value->ID);
				}
			}			
			
			$berkas = array();	
			$berkas[] = (object) array('label'=>'lengkap', 'value'=>'lengkap');
			$berkas[] = (object) array('label'=>'tidak lengkap', 'value'=>'tidak lengkap');
			
			$fields = array();
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'no register',
				'name' 			=> 'no',
				'placeholder'	=> 'no register',
				'value' 		=> '',
				'classes' 		=> '',
			);	
			$fields[] = (object) array(
				'type' 			=> 'date',
				'label' 		=> 'tgl register',
				'name' 			=> 'tgl',
				'placeholder'	=> 'tgl register',
				'format'		=> 'YYYY-MM-DD',
				'value' 		=> '',
				'classes' 		=> '',
			);
			$fields[] = (object) array(
				'type' 			=> 'select',
				'label' 		=> 'litmas',
				'name' 			=> 'litmas',
				'placeholder'	=> '--pilih litmas--',
				'options'		=>  $litmas,
				'value' 		=> '',
				'classes' 		=> '',
			);				
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'nama',
				'name' 			=> 'nama',
				'placeholder'	=> 'nama',
				'value' 		=> '',
				'classes' 		=> '',
			);				
			$fields[] = (object) array(
				'type' 			=> 'textarea',
				'label' 		=> 'kasus',
				'name' 			=> 'kasus',
				'placeholder'	=> 'kasus',
				'value' 		=> '',
				'classes' 		=> 'full-width',
			);
			$fields[] = (object) array(
				'type' 			=> 'select',
				'label' 		=> 'kelengkapan berkas',
				'name' 			=> 'berkas',
				'placeholder'	=> '--pilih kelengkapan berkas--',
				'options'		=>  $berkas,
				'value' 		=> '',
				'classes' 		=> '',
			);		
			$fields[] = (object) array(
				'type' 			=> 'select',
				'label' 		=> 'jenis pembimbingan',
				'name' 			=> 'jenis',
				'placeholder'	=> '--pilih jenis pembimbingan--',
				'options'		=>  $jenis,
				'value' 		=> '',
				'classes' 		=> '',
			);				
			

			$this->data['insert'] = (object) array (
				'type'  	=> 'insert_default',
				'data'		=> (object) array (
					'classes'  	=> '',
					'fields'  	=> $fields,
				)
			);	
			echo json_encode($this->data['insert']);				
		}
	}
	
	public function update(){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}		
		if(isset($_POST['submit'])){
			//validation
			$error_info = array();
			$error_status = false;
			if($_POST['no'] == ''){
				$error_info[] = 'no register tidak boleh kosong';
				$error_status = true;
			}
			if($_POST['tgl'] == ''){
				$error_info[] = 'tgl register tidak boleh kosong';
				$error_status = true;
			}
			if($_POST['nama'] == ''){
				$error_info[] = 'nama tidak boleh kosong';
				$error_status = true;
			}
			if($_POST['kasus'] == ''){
				$error_info[] = 'kasus tidak boleh kosong';
				$error_status = true;
			}
			if($_POST['berkas'] == ''){
				$error_info[] = 'keterangan berkas tidak boleh kosong';
				$error_status = true;
			}
			if($_POST['litmas'] == ''){
				$error_info[] = 'litmas tidak boleh kosong';
				$error_status = true;
			}
			if($_POST['jenis'] == ''){
				$error_info[] = 'jenis pembimbingan tidak boleh kosong';
				$error_status = true;
			}
			
			if($error_status == true){
				$this->data['error'] = (object) array (
					'type'  	=> 'error',
					'data'		=> (object) array (
						'info'	=> $error_info,
					)
				);				
				echo json_encode($this->data['error']);
			}else{
				$this->data['update'] = array(
					'NO_REGISTER' => $_POST['no'],
					'TGL_REGISTER' => $_POST['tgl'],
					'NAMA' => $_POST['nama'],
					'KASUS' => $_POST['kasus'],
					'KET_BERKAS' => $_POST['berkas'],
					'LITMAS_ID' => $_POST['litmas'],
					'JENIS_PEMBIMBINGAN_ID' => $_POST['jenis'],
					'USER_ID' => $this->session->userdata('ID'),
					);				
				$result = $this->pembimbingan_model->update($this->data['update'], $_POST['id']);
				if($result == true){
					$info = array();
					$info[] = 'Update data successfully';						
					$this->data['info'] = (object) array (
						'type'  	=> 'success',
						'data'		=> (object) array (
							'info'	=> $info,
						)
					);
				}else{
					$info = array();
					$info[] = 'Update data not successfull';
					$this->data['info'] = (object) array (
						'type'  	=> 'error',
						'data'		=> (object) array (
							'info'	=> $info,
						)
					);
				}				
				echo json_encode($this->data['info']);			
			}			
		}else{
			$r_no = '';
			$r_tgl = '';
			$r_nama = '';
			$r_litmas = '';
			$r_berkas = '';
			$r_jenis = '';
			$r_kasus = '';
			
			$filter = array();
			$filter[] = "A.ID = ". $_POST['id'];
			$this->data['result'] = $this->pembimbingan_model->get($filter);
			foreach($this->data['result'] as $value){
				$r_id 	= $value->ID;
				$r_no = $value->NO_REGISTER;
				$r_tgl = $value->TGL_REGISTER;
				$r_nama = $value->NAMA;
				$r_litmas = $value->LITMAS_ID;
				$r_berkas = $value->KET_BERKAS;
				$r_jenis = $value->JENIS_PEMBIMBINGAN_ID;
				$r_kasus = $value->KASUS;
			}
			
			
			
			$litmas = array();
			$filter = array();
			$filter[] = " STATUS = '1' "; 
			$data = $this->litmas_model->get($filter);
			
			if (empty($data)) {

			} else {
				foreach ($data as $value) {
					$litmas[] = (object) array('label'=>$value->NAMA_LITMAS, 'value'=>$value->ID);
				}
			}

			$jenis = array();
			$data = $this->jenispembimbingan_model->get();
			
			if (empty($data)) {

			} else {
				foreach ($data as $value) {
					$jenis[] = (object) array('label'=>$value->NAMA_JENIS, 'value'=>$value->ID);
				}
			}			
			
			$berkas = array();	
			$berkas[] = (object) array('label'=>'lengkap', 'value'=>'lengkap');
			$berkas[] = (object) array('label'=>'tidak lengkap', 'value'=>'tidak lengkap');
			
			$fields = array();
			$fields[] = (object) array(
				'type' 		=> 'hidden',
				'label' 	=> 'id',
				'name' 		=> 'id',
				'value' 	=> $r_id,
				'classes' 	=> '',
			);				
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'no register',
				'name' 			=> 'no',
				'placeholder'	=> 'no register',
				'value' 		=> $r_no,
				'classes' 		=> '',
			);	
			$fields[] = (object) array(
				'type' 			=> 'date',
				'label' 		=> 'tgl register',
				'name' 			=> 'tgl',
				'placeholder'	=> 'tgl register',
				'format'		=> 'YYYY-MM-DD',
				'value' 		=> $r_tgl,
				'classes' 		=> '',
			);
			$fields[] = (object) array(
				'type' 			=> 'select',
				'label' 		=> 'litmas',
				'name' 			=> 'litmas',
				'placeholder'	=> '--pilih litmas--',
				'options'		=>  $litmas,
				'value' 		=> $r_litmas,
				'classes' 		=> '',
			);				
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'nama',
				'name' 			=> 'nama',
				'placeholder'	=> 'nama',
				'value' 		=> $r_nama,
				'classes' 		=> '',
			);				
			$fields[] = (object) array(
				'type' 			=> 'textarea',
				'label' 		=> 'kasus',
				'name' 			=> 'kasus',
				'placeholder'	=> 'kasus',
				'value' 		=> $r_kasus,
				'classes' 		=> 'full-width',
			);
			$fields[] = (object) array(
				'type' 			=> 'select',
				'label' 		=> 'kelengkapan berkas',
				'name' 			=> 'berkas',
				'placeholder'	=> '--pilih kelengkapan berkas--',
				'options'		=>  $berkas,
				'value' 		=> $r_berkas,
				'classes' 		=> '',
			);		
			$fields[] = (object) array(
				'type' 			=> 'select',
				'label' 		=> 'jenis pembimbingan',
				'name' 			=> 'jenis',
				'placeholder'	=> '--pilih jenis pembimbingan--',
				'options'		=>  $jenis,
				'value' 		=> $r_jenis,
				'classes' 		=> '',
			);	

			$this->data['update'] = (object) array (
				'type'  	=> 'update_default',
				'data'		=> (object) array (
					'classes'  	=> '',
					'fields'  	=> $fields,
				)
			);
			echo json_encode($this->data['update']);
		}
	}
	
	public function detail($id=null){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}		
		if(isset($_POST['id']) and $_POST['id'] != null){
			$filters = array();
			$filters[] = "A.ID = ". $_POST['id'];
			$data = $this->pembimbingan_model->get($filters);
			
			$body= array();			
			if (empty($data)) {
                $body[] = array(
                    (object) array ('colspan' => 100, 'classes' => ' empty bold align-center', 'value' => 'No Data')
                );
			} else {
				foreach($data as $value){
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'No Register' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->NO_REGISTER ),
					);
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Tgl Register' ),
						(object) array( 'classes' => ' align-left ', 'value' => date("d M Y", strtotime($value->TGL_REGISTER)) ),
					);
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Nama' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->NAMA ),
					);
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Kasus' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->KASUS ),
					);					
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Litmas' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->NAMA_LITMAS ),
					);
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Jenis Pembimbingan' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->NAMA_JENIS ),
					);
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Lama / Proses' ),
						(object) array( 'classes' => ' align-left ', 'value' => (new DateTime(date('Y-m-d')))->diff(new DateTime($value->TGL_REGISTER))->format('%a hari') ),
					);	
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Keterangan Berkas' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->KET_BERKAS ),
					);						
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Status' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->STATUS ),
					);
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Create Date' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->CREATE_DATE ),
					);
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Update Date' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->UPDATE_DATE ),
					);					
				}
			}
	
			$header = array(
				array (
					(object) array ('rowspan' => 1, 'classes' => 'bold align-left capitalize', 'value' => 'Label'),
					(object) array ('colspan' => 1, 'classes' => 'bold align-left capitalize', 'value' => 'Value'),	
				)		
			);			
			
			$this->data['detail'] = (object) array (
				'type'  	=> 'detail_default',
				'data'		=> (object) array (
					'classes'	=> 'striped bordered hover',
					'header'	=> $header,
					'body'		=> $body,
				)
			);			
			echo json_encode($this->data['detail']);
		}
	}
	
 	public function update_status(){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}		
		if(isset($_POST['id']) and $_POST['id'] != null){
			$filters = array();
			$filters[] = "A.ID = ". $_POST['id'];
			
			$result = $this->pembimbingan_model->get($filters);
			if($result != null){
				foreach($result as $item){
					$status = $item->STATUS;
				}
				if($status == '1'){
					$new_status = '0';
				}else if($status == '0'){
					$new_status = '1';
				}
			}
			
			$this->data['update'] = array(
					'STATUS' => $new_status,
				);	
				
			$result = $this->litmas_model->update($this->data['update'], $_POST['id']);
			if($result == true){
				$info = array();
				$info[] = 'Update status data successfully';						
				$this->data['info'] = (object) array (
					'type'  	=> 'success',
					'data'		=> (object) array (
						'info'	=> $info,
					)
				);
			}else{
				$info = array();
				$info[] = 'Update status data not successfull';
				$this->data['info'] = (object) array (
					'type'  	=> 'error',
					'data'		=> (object) array (
						'info'	=> $info,
					)
				);
			}			
			echo json_encode($this->data['info']);	
		}
	}
	
	public function delete(){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}		
		$this->data['delete'] = array(
				'ID' => $_POST['id'],
			);		
		$result = $this->pembimbingan_model->delete($this->data['delete']);
		
		if($result == true){
			$info = array();
			$info[] = 'Delete data successfully';			
			$info[] = 'Have a nice day';			
			$this->data['info'] = (object) array (
				'type'  	=> 'success',
				'data'		=> (object) array (
					'info'	=> $info,
				)
			);
		}else{
			$info = array();
			$info[] = 'Delete data not successfull';
			$this->data['info'] = (object) array (
				'type'  	=> 'error',
				'data'		=> (object) array (
					'info'	=> $info,
				)
			);
		}
		echo json_encode($this->data['info']);			
	}
	
}

