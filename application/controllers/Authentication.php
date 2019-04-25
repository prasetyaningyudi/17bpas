<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Controller {
	private $data;
	
	public function __construct(){
		parent::__construct();
		$this->load->library('session');		
		$this->load->helper('url');			
		$this->load->database();
		$this->load->model('user_model');
		$this->load->model('app_data_model');		
		$this->data['app_data'] = $this->app_data_model->get();			
		$this->data['error'] = array();
		$this->data['title'] = 'Authentication';	
	}
	
	public function index(){
		$this->login();
	}
	
	public function login(){
		if($this->session->userdata('is_logged_in') == 1){
			redirect ('authentication/unauthorized');
		}
		if(isset($_POST['login-submit'])){
			$error_info = array();
			$error_status = false;			
			if($_POST['username'] == ''){
				$error_info[] = 'Username can not be null';
				$error_status = true;
			}
			if($_POST['password'] == ''){
				$error_info[] = 'Password can not be null';
				$error_status = true;
			}
			$filters = array();
			$filters[] = "A.USERNAME = '".$_POST['username']."'";
			$filters[] = "A.PASSWORD = '".md5($_POST['password'])."'";
			$filters[] = "A.STATUS = '1'";
			
			$data = $this->user_model->get($filters);	
			if(empty($data)){
				$error_info[] = 'Wrong Username or Password!';
				$error_status = true;				
			}
			
			if($error_status == true){
				$this->data['error'] = (object) array (
					'status'	=> $error_status,
					'info'		=> $error_info,
				);				
				//echo json_encode($this->data['error']);
				$this->data['subtitle'] = 'Login';
				$this->data['class'] = __CLASS__;		
				$this->load->view('section_header', $this->data);
				$this->load->view('page_login');				
			}else{
				foreach($data as $item){
					$data_session=array(
						'ID'=>$item->ID,
						'USERNAME'=>$item->USERNAME,
						'ROLE_ID'=>$item->BID,
						'ROLE_NAME'=>strtolower($item->ROLE_NAME),
						'PHOTO'=>$item->PHOTO_1,
						'is_logged_in'=>1
					);					
				}
				//create session
				$this->session->set_userdata($data_session);
				redirect('home');
			}
		}else{
			$this->data['subtitle'] = 'Login';
			$this->data['class'] = __CLASS__;		
			$this->load->view('section_header', $this->data);
			$this->load->view('page_login');
		}
	}
	
	public function logout(){	
		$this->session->sess_destroy();
		redirect('authentication');
	}

	public function unauthorized(){
		$this->data['subtitle'] = 'Unauthorized Access';
		$this->data['class'] = __CLASS__;		
		$this->load->view('section_header', $this->data);
		$this->load->view('page_unauthorized');			
	}
}
