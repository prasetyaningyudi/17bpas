<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengajuan extends CI_Controller {
	private $data;
	
	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->library('auth');			
		$this->load->helper('url');			
		$this->load->database();
		$this->load->model('litmas_model');
		$this->load->model('jenispembimbingan_model');
		$this->load->model('pengajuan_model');
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
		$this->data['title'] = 'Pengajuan Litmas';
		//var_dump($this->data['menu']);
	}

	public function index(){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}		
		$this->data['subtitle'] = '';
		$this->data['class'] = __CLASS__;
		$this->load->view('section_header', $this->data);
		$this->load->view('section_sidebar');
		$this->load->view('section_nav');
		$this->load->view('main_index');	
		$this->load->view('section_footer');			
	}

	public function lists(){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}		
		$filters = array();
		$limit = array('20', '0');
		$r_dari = '';
		$r_perihal = '';

		//var_dump($_POST['nama']);
		if(isset($_POST['submit'])){
			if (isset($_POST['dari'])) {
				if ($_POST['dari'] != '' or $_POST['dari'] != null) {
					$filters[] = "DARI LIKE '%" . $_POST['dari'] . "%'";
					$r_dari = $_POST['dari'];
				}
			}
			if (isset($_POST['perihal'])) {
				if ($_POST['perihal'] != '' or $_POST['perihal'] != null) {
					$filters[] = "PERIHAL LIKE '%" . $_POST['perihal'] . "%'";
					$r_perihal = $_POST['perihal'];
				}
			}						
			if (isset($_POST['offset'])) {
				if ($_POST['offset'] != '' or $_POST['offset'] != null) {
					$limit[1] = $_POST['offset'];
				}
			}			
		}
			

		if($this->session->userdata('ROLE_NAME') == '' or $this->session->userdata('ROLE_NAME') == null){
			
			$header = array(
				array (
					(object) array ('rowspan' => 1, 'classes' => 'bold align-left capitalize', 'value' => ''),			
				)		
			);	

			$body = array(
				array (
					(object) array ('rowspan' => 1, 'classes' => 'bold align-left', 'value' => 'Untuk mengajukan Litmas, <br>Gunakan tombol ADD di samping kanan atas.'),			
				)		
			);			
						
			
			$this->data['list'] = (object) array (
				'type'  	=> 'table_default',
				'data'		=> (object) array (
					'classes'  	=> 'striped bordered hover',
					'insertable'=> true,
					'editable'	=> false,
					'deletable'	=> false,
					'statusable'=> false,
					'detailable'=> false,
					'pdf'		=> false,
					'xls'		=> false,
					'title'		=> 'Pengajuan Litmas',
					'pagination'=> null,
					'filters'  	=> null,
					'toolbars'	=> null,
					'header'  	=> $header,
					'body'  	=> $body,
					'footer'  	=> null,
				)
			);
		}else{
			$data = $this->pengajuan_model->get($filters, $limit);
			//var_dump($data);
			$total_data = count($this->pengajuan_model->get($filters));
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
							(object) array( 'classes' => ' align-left ', 'value' => $value->DARI ),
							(object) array( 'classes' => ' align-left ', 'value' => $value->PERIHAL ),
							(object) array( 'classes' => ' align-left ', 'value' => $value->TELEPON ),
							(object) array( 'classes' => ' align-left ', 'value' => '<a target="_blank" href="'.$value->FILE.'">'.$value->FILE.'</a>' ),
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
					(object) array ('colspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'dari'),								
					(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'perihal'),			
					(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'telepon'),			
					(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'file'),				
				)		
			);			
			
			$fields = array();
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'dari',
				'name' 			=> 'dari',
				'placeholder'	=> 'dari',
				'value' 		=> $r_dari,
				'classes' 		=> 'full-width',
			);	
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'perihal',
				'name' 			=> 'perihal',
				'placeholder'	=> 'perihal',
				'value' 		=> $r_perihal,
				'classes' 		=> 'full-width',
			);				
			
			$this->data['list'] = (object) array (
				'type'  	=> 'table_default',
				'data'		=> (object) array (
					'classes'  	=> 'striped bordered hover',
					'insertable'=> true,
					'editable'	=> false,
					'deletable'	=> true,
					'statusable'=> false,
					'detailable'=> true,
					'pdf'		=> false,
					'xls'		=> true,
					'title'		=> 'Pengajuan Litmas',
					'pagination'=> $limit,
					'filters'  	=> $fields,
					'toolbars'	=> null,
					'header'  	=> $header,
					'body'  	=> $body,
					'footer'  	=> null,
				)
			);
		}
		
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
		date_default_timezone_set("Asia/Jakarta");
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}		
		if(isset($_POST['submit'])){
			$error_info = array();
			$error_status = false;
			if($_POST['dari'] == ''){
				$error_info[] = 'dari tidak boleh kosong';
				$error_status = true;
			}
			if($_POST['perihal'] == ''){
				$error_info[] = 'perihal tidak boleh kosong';
				$error_status = true;
			}
			if($_POST['telepon'] == ''){
				$error_info[] = 'telepon tidak boleh kosong';
				$error_status = true;
			}
			if(isset($_FILES["berkas"])){
				if($_FILES["berkas"] != null){
					if($_FILES["berkas"]["size"] > '5000000'){
						$error_info[] = 'Maksimum ukuran file 5mb';
						$error_status = true;								
					}
					if($error_status == false){
						//upload file
						$filename = $_FILES['berkas']['name'];
						$target_dir = FCPATH."public/files/";
						$uniq = date('YmdHis');
						$rename = $uniq . '_' . $filename;
						$success_upload = move_uploaded_file($_FILES["berkas"]["tmp_name"], $target_dir . $rename);
						
						if(!$success_upload){
							$error_info[] = 'Error upload photo';
							$error_status = true;
						}else{
							$file = base_url().'public/files/'.$rename;
						}
					}					
				}
			}else{
				$error_info[] = 'berkas pengajuan tidak boleh kosong';
				$error_status = true;					
			}
			if($_POST['jawaban'] == ''){
				$error_info[] = 'Jawaban tidak boleh kosong';
				$error_status = true;
			}else{
				if( $_POST['jawaban'] != ($_POST['soal1'] + $_POST['soal2']) ){
					$error_info[] = 'Jawaban salah';
					$error_status = true;					
				}
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
					'DARI' => $_POST['dari'],
					'PERIHAL' => $_POST['perihal'],
					'TELEPON' => $_POST['telepon'],
					'FILE' => $file,
				);	
				//var_dump($this->data['insert']);die;
				$result = $this->pengajuan_model->insert($this->data['insert']);
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
			
			$berkas = array();	
			$berkas[] = (object) array('label'=>'lengkap', 'value'=>'lengkap');
			$berkas[] = (object) array('label'=>'tidak lengkap', 'value'=>'tidak lengkap');
			
			$fields = array();
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'dari',
				'name' 			=> 'dari',
				'placeholder'	=> 'dari',
				'value' 		=> '',
				'classes' 		=> '',
			);
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'telepon',
				'name' 			=> 'telepon',
				'placeholder'	=> 'telepon',
				'value' 		=> '',
				'classes' 		=> '',
			);				
			$fields[] = (object) array(
				'type' 			=> 'textarea',
				'label' 		=> 'perihal',
				'name' 			=> 'perihal',
				'placeholder'	=> 'perihal',
				'value' 		=> '',
				'classes' 		=> 'full-width',
			);			
			$fields[] = (object) array(
				'type' 			=> 'file',
				'label' 		=> 'berkas',
				'name' 			=> 'berkas',
				'placeholder'	=> 'berkas',
				'value' 		=> '',
				'classes' 		=> 'full-width',
			);
			$soal1 = rand(1,10);
			$soal2 = rand(1,10);
			$fields[] = (object) array(
				'type' 			=> 'hidden',
				'label' 		=> 'soal1',
				'name' 			=> 'soal1',
				'placeholder'	=> 'soal1',
				'value' 		=> $soal1,
				'classes' 		=> 'full-width',
			);
			$fields[] = (object) array(
				'type' 			=> 'hidden',
				'label' 		=> 'soal2',
				'name' 			=> 'soal2',
				'placeholder'	=> 'soal2',
				'value' 		=> $soal2,
				'classes' 		=> 'full-width',
			);			
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'Jumlahkan ' . $soal1 . ' + ' . $soal2 .  ' ? ',
				'name' 			=> 'jawaban',
				'placeholder'	=> $soal1 . ' + ' . $soal2,
				'value' 		=> '',
				'classes' 		=> 'full-width',
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
	
	public function detail($id=null){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}		
		if(isset($_POST['id']) and $_POST['id'] != null){
			$filters = array();
			$filters[] = "A.ID = ". $_POST['id'];
			$data = $this->pengajuan_model->get($filters);
			
			$body= array();			
			if (empty($data)) {
                $body[] = array(
                    (object) array ('colspan' => 100, 'classes' => ' empty bold align-center', 'value' => 'No Data')
                );
			} else {
				foreach($data as $value){
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Dari' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->DARI ),
					);
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Perihal' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->PERIHAL ),
					);
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Telepon' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->TELEPON ),
					);					
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'File Berkas' ),
						(object) array( 'classes' => ' align-left ', 'value' => '<a target="_blank" href="'.$value->FILE.'">'.$value->FILE.'</a>' ),
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
	
	public function delete(){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}		
		$this->data['delete'] = array(
				'ID' => $_POST['id'],
			);		
		$result = $this->pengajuan_model->delete($this->data['delete']);
		
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

