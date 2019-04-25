<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Assignmenu extends CI_Controller {
	private $data;
	
	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->library('auth');				
		$this->load->helper('url');			
		$this->load->database();
		$this->load->model('assignmenu_model');
		$this->load->model('role_model');
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
		$this->data['title'] = 'Assign Menu to Role';
		//var_dump($this->data['menu']);
	}

	public function index(){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}		
		$this->data['subtitle'] = 'List';
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
		$limit = array('10', '0');
		$r_menuname = '';
		$r_rolename = '';

		if(isset($_POST['submit'])){
			if (isset($_POST['menu_name'])) {
				if ($_POST['menu_name'] != '' or $_POST['menu_name'] != null) {
					$filters[] = "A.MENU_ID = '" . $_POST['menu_name'] . "'";
					$r_menuname = $_POST['menu_name'];
				}
			}
			if (isset($_POST['role_name'])) {
				if ($_POST['role_name'] != '' or $_POST['role_name'] != null) {
					$filters[] = "A.ROLE_ID = '" . $_POST['role_name'] . "'";
					$r_rolename = $_POST['role_name'];
				}
			}
			if (isset($_POST['offset'])) {
				if ($_POST['offset'] != '' or $_POST['offset'] != null) {
					$limit[1] = $_POST['offset'];
				}
			}			
		}
		
		$data = $this->assignmenu_model->get($filters, $limit);
		$total_data = count($this->assignmenu_model->get($filters));
		$limit[] = $total_data;
		
		//var_dump($data);

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
						(object) array( 'classes' => ' bold align-left ', 'value' => $no_body+1 ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->MENU_ID ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->ROLE_ID ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->MENU_NAME ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->ROLE_NAME ),
					);
					$no_body++;
				}
			}
        } else {
            $body[$no_body] = array(
                (object) array ('colspan' => 100, 'classes' => ' empty bold align-center', 'value' => '')
            );
        }
		
		$header = array(
			array (
				(object) array ('rowspan' => 1, 'classes' => 'bold align-left capitalize', 'value' => 'No'),
				(object) array ('colspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'menu id'),					
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'role id'),			
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'menu name'),			
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'role name'),		
			)		
		);

		$menu_name = array();
		$filter = array();
		$filter[] = " A.STATUS = '1'";
		$data = $this->menu_model->get($filter);
		if (empty($data)) {
			
		} else {
			foreach ($data as $value) {
				$menu_name[] = (object) array('label'=>$value->MENU_NAME, 'value'=>$value->ID);
			}
		}	
		
		$role_name = array();
		$filter = array();
		$filter[] = " STATUS = '1'";
		$data = $this->role_model->get($filter);
		if (empty($data)) {
			
		} else {
			foreach ($data as $value) {
				$role_name[] = (object) array('label'=>$value->ROLE_NAME, 'value'=>$value->ID);
			}
		}		
			
		$fields = array();
		$fields[] = (object) array(
			'type' 			=> 'select',
			'label' 		=> 'Menu name',
			'name' 			=> 'menu name',
			'placeholder'	=> '--Select Menu--',
			'value' 		=> $r_menuname,
			'options'		=> $menu_name,
			'classes' 		=> 'full-width
			',
		);			
		$fields[] = (object) array(
			'type' 			=> 'select',
			'label' 		=> 'Role Name',
			'name' 			=> 'role_name',
			'placeholder'	=> '--Select Role--',
			'value' 		=> $r_rolename,
			'options'		=> $role_name,
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
				'detailable'=> false,
				'pdf'		=> false,
				'xls'		=> false,
				'pagination'=> $limit,
				'filters'  	=> $fields,
				'toolbars'	=> null,
				'header'  	=> $header,
				'body'  	=> $body,
				'footer'  	=> null,
			)		
		);		
		echo json_encode($this->data['list']);
	}
	
	public function insert(){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}			
		if(isset($_POST['submit'])){
			//validation
			$error_info = array();
			$error_status = false;
			if($_POST['menu_name'] == ''){
				$error_info[] = 'Menu Name can not be null';
				$error_status = true;
			}
			if($_POST['role_name'] == ''){
				$error_info[] = 'Role name can not be null';
				$error_status = true;
			}
			$filter = array();
			$filter[] = "A.MENU_ID = '". $_POST['menu_name']."'";
			$filter[] = "A.ROLE_ID = '". $_POST['role_name']."'";
			$data = $this->assignmenu_model->get($filter);
			if(!empty($data)){
				$error_info[] = 'This pair has been inputted';
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
						'MENU_ID' => $_POST['menu_name'],
						'ROLE_ID' => $_POST['role_name'],
					);						

				$result = $this->assignmenu_model->insert($this->data['insert']);
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
			$menu_name = array();
			$filter = array();
			$filter[] = " A.STATUS = '1'";
			$data = $this->menu_model->get($filter);
			if (empty($data)) {
				
			} else {
				foreach ($data as $value) {
					$menu_name[] = (object) array('label'=>$value->MENU_NAME, 'value'=>$value->ID);
				}
			}	
			
			$role_name = array();
			$filter = array();
			$filter[] = " STATUS = '1'";
			$data = $this->role_model->get($filter);
			if (empty($data)) {
				
			} else {
				foreach ($data as $value) {
					$role_name[] = (object) array('label'=>$value->ROLE_NAME, 'value'=>$value->ID);
				}
			}		
				
			$fields = array();
			$fields[] = (object) array(
				'type' 			=> 'select',
				'label' 		=> 'Menu name',
				'name' 			=> 'menu name',
				'placeholder'	=> '--Select Menu--',
				'value' 		=> '',
				'options'		=> $menu_name,
				'classes' 		=> '',
			);			
			$fields[] = (object) array(
				'type' 			=> 'select',
				'label' 		=> 'Role Name',
				'name' 			=> 'role_name',
				'placeholder'	=> '--Select Role--',
				'value' 		=> '',
				'options'		=> $role_name,
				'classes' 		=> '',
			);			
			

			$this->data['insert'] = (object) array (
				'type'  	=> 'modal',
				'classes'  	=> '',
				'fields'  	=> $fields,
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
			$error_info = array();
			$error_status = false;
			if($_POST['menu_name'] == ''){
				$error_info[] = 'Menu Name can not be null';
				$error_status = true;
			}
			if($_POST['role_name'] == ''){
				$error_info[] = 'Role name can not be null';
				$error_status = true;
			}
			$filter = array();
			$filter[] = "A.MENU_ID = '". $_POST['menu_name']."'";
			$filter[] = "A.ROLE_ID = '". $_POST['role_name']."'";
			$data = $this->assignmenu_model->get($filter);
			if(!empty($data)){
				$error_info[] = 'This pair has been inputted';
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
						'MENU_ID' => $_POST['menu_name'],
						'ROLE_ID' => $_POST['role_name'],
					);						

				$result = $this->assignmenu_model->update($this->data['update'], $_POST['id']);
				echo json_encode($result);
					$info = array();
					$info[] = 'Update data successfully';						
					$this->data['info'] = (object) array (
						'type'  	=> 'success',
						'data'		=> (object) array (
							'info'	=> $info,
						)
					);				
			}	
		}else{
			$r_menuname = '';
			$r_rolename = '';
			
			$filter = array();
			$filter[] = "A.ID = ". $_POST['id'];
			$this->data['result'] = $this->assignmenu_model->get($filter);
			foreach($this->data['result'] as $value){
				$r_id 	= $value->ID;
				$r_menuname = $value->MENU_ID;
				$r_rolename = $value->ROLE_ID;				
			}
			
			$menu_name = array();
			$filter = array();
			$filter[] = " A.STATUS = '1'";
			$data = $this->menu_model->get($filter);
			if (empty($data)) {
				
			} else {
				foreach ($data as $value) {
					$menu_name[] = (object) array('label'=>$value->MENU_NAME, 'value'=>$value->ID);
				}
			}	
			
			$role_name = array();
			$filter = array();
			$filter[] = " STATUS = '1'";
			$data = $this->role_model->get($filter);
			if (empty($data)) {
				
			} else {
				foreach ($data as $value) {
					$role_name[] = (object) array('label'=>$value->ROLE_NAME, 'value'=>$value->ID);
				}
			}
			
			$fields = array();
			$fields[] = (object) array(
				'type' 		=> 'hidden',
				'label' 	=> 'id',
				'name' 		=> 'id',
				'value' 	=> $r_id,
				'classes' 	=> '',
			);				
			$fields[] = (object) array(
				'type' 			=> 'select',
				'label' 		=> 'Menu name',
				'name' 			=> 'menu name',
				'placeholder'	=> '--Select Menu--',
				'value' 		=> $r_menuname,
				'options'		=> $menu_name,
				'classes' 		=> '',
			);			
			$fields[] = (object) array(
				'type' 			=> 'select',
				'label' 		=> 'Role Name',
				'name' 			=> 'role_name',
				'placeholder'	=> '--Select Role--',
				'value' 		=> $r_rolename,
				'options'		=> $role_name,
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
	
	public function update_status(){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}	
	}
	
	public function delete(){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}			
		$this->data['delete'] = array(
				'ID' => $_POST['id'],
			);		
		$result = $this->assignmenu_model->delete($this->data['delete']);
		echo json_encode($result);

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

