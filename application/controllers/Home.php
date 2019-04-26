<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
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
	}

	public function index(){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}		
		$this->data['subtitle'] = 'Rekap Data';
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
		$limit = array('50', '0');
		$r_jenis = '';

		//var_dump($_POST['nama']);
		if(isset($_POST['submit'])){
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
		
		$data = $this->pembimbingan_model->get_rekap($filters, $limit);
		//var_dump($data);
		$total_data = count($this->pembimbingan_model->get_rekap($filters));
		$limit[] = $total_data;
		
		//var_dump($data);
		$jumlah = 0;
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
						(object) array( 'classes' => ' bold align-center ', 'value' => $limit[1] + ($no_body+1) ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->NAMA_LITMAS ),
						(object) array( 'classes' => ' align-right ', 'value' => $value->JUMLAH ),
					);
					$no_body++;
					$jumlah += $value->JUMLAH;
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
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'litmas'),			
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'jumlah'),	
			)		
		);
		$footer = array(
			array (	
				(object) array ('rowspan' => 1, 'colspan' => 2, 'classes' => 'bold align-center capitalize', 'value' => 'total'),			
				(object) array ('rowspan' => 1, 'classes' => 'bold align-right capitalize', 'value' => $jumlah ),	
			)		
		);		

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
				'insertable'=> false,
				'editable'	=> false,
				'deletable'	=> false,
				'statusable'=> false,
				'detailable'=> false,
				'pdf'		=> false,
				'xls'		=> true,
				'title'		=> 'Rekap Data Pembimbingan',
				'pagination'=> $limit,
				'filters'  	=> $fields,
				'toolbars'	=> null,
				'header'  	=> $header,
				'body'  	=> $body,
				'footer'  	=> $footer,
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
	
}

