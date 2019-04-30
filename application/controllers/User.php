<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
	private $data;
	
	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->library('auth');			
		$this->load->helper('url');			
		$this->load->database();
		$this->load->model('role_model');
		$this->load->model('user_model');
		$this->load->model('user_info_model');
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
		$this->data['title'] = 'User';
		//var_dump($this->data['menu']);
	}

	public function index(){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}
		if($this->session->userdata('ROLE_NAME') == 'administrator'){
			$this->data['subtitle'] = 'List';
		}else{
			$this->data['subtitle'] = 'Profile';
		}
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
		$r_username = '';
		$r_role = '';
		$r_alias = '';
		$r_status = '';

		if($this->session->userdata('ROLE_NAME') != 'administrator'){
			$filters[] = "A.ID = '" . $this->session->userdata('ID') . "'";
		}
		$filters[] = "A.USERNAME != 'prsty'";		
		
		//var_dump($_POST['nama']);
		if(isset($_POST['submit'])){
			if (isset($_POST['username'])) {
				if ($_POST['username'] != '' or $_POST['username'] != null) {
					$filters[] = "A.USERNAME LIKE '%" . $_POST['username'] . "%'";
					$r_username = $_POST['username'];
				}
			}			
			if (isset($_POST['role'])) {
				if ($_POST['role'] != '' or $_POST['role'] != null) {
					$filters[] = "A.ROLE_ID = '" . $_POST['role'] . "'";
					$r_role = $_POST['role'];
				}
			}
			if (isset($_POST['status'])) {
				if ($_POST['status'] != '' or $_POST['status'] != null) {
					$filters[] = "A.STATUS = '" . $_POST['status'] . "'";
					$r_status = $_POST['status'];
				}
			}
			if (isset($_POST['offset'])) {
				if ($_POST['offset'] != '' or $_POST['offset'] != null) {
					$limit[1] = $_POST['offset'];
				}
			}			
		}
		
		$data = $this->user_model->get($filters, $limit);
		$total_data = count($this->user_model->get($filters));
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
						(object) array( 'classes' => ' align-left ', 'value' => $value->USERNAME ),
						(object) array( 'classes' => ' align-center ', 'value' => $value->STATUS ),
						(object) array( 'classes' => ' align-center ', 'value' => $value->ROLE_NAME ),
						(object) array( 'classes' => ' align-center ', 'value' => '<a href="javascript:void(0)" title="edit" onclick="show_modal(\''.base_url().'user/m_form_user_info/'.$value->ID.'/\')"><i style="font-size: 16px;" class="fas fa-user-edit"></i></a>' ),
						(object) array( 'classes' => ' align-center ', 'value' => '<a href="javascript:void(0)" title="info" onclick="show_modal(\''.base_url().'user/m_user_info/'.$value->ID.'/\')">
						<i style="font-size: 16px;" class="fas fa-user-tag"></i></a>' ),
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
				(object) array ('rowspan' => 1, 'classes' => 'bold align-left capitalize', 'value' => 'No'),
				(object) array ('colspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'username'),										
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'status'),			
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'role'),			
				(object) array ('rowspan' => 1, 'colspan' => '2' ,'classes' => 'bold align-center capitalize', 'value' => 'User Info'),			
			)		
		);

		$role = array();
		$filter = array();
		$filter[] = "STATUS ='1'";
		$data = $this->role_model->get($filter);
		
		if (empty($data)) {
			//$parent[] = (object) array('label'=>'No Data', 'value'=>'nodata');
		} else {
			foreach ($data as $value) {
				$role[] = (object) array('label'=>$value->ROLE_NAME, 'value'=>$value->ID);
			}
		}	
			
		$fields = array();
		$fields[] = (object) array(
			'type' 			=> 'text',
			'label' 		=> 'Username',
			'placeholder' 	=> 'username',
			'name' 			=> 'username',
			'value' 		=> $r_username,
			'classes' 		=> 'full-width',
		);
		$fields[] = (object) array(
			'type' 			=> 'text',
			'label' 		=> 'Status',
			'placeholder' 	=> 'user status',
			'name' 			=> 'status',
			'value' 		=> $r_status,
			'classes' 		=> 'full-width',
		);
		$fields[] = (object) array(
			'type' 			=> 'select',
			'label' 		=> 'Role',
			'name' 			=> 'role',
			'placeholder'	=> '--Select Role--',
			'value' 		=> $r_role,
			'options'		=> $role,
			'classes' 		=> 'full-width',
		);	
		
		if($this->session->userdata('ROLE_NAME') == 'administrator'){
			$this->data['list'] = (object) array (
				'type'  	=> 'table_default',
				'data'		=> (object) array (
					'classes'  	=> 'striped bordered hover',
					'insertable'=> true,
					'editable'	=> true,
					'deletable'	=> true,
					'statusable'=> true,
					'detailable'=> true,
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
		}else{
			$this->data['list'] = (object) array (
				'type'  	=> 'table_default',
				'data'		=> (object) array (
					'classes'  	=> 'striped bordered hover',
					'insertable'=> false,
					'editable'	=> true,
					'deletable'	=> false,
					'statusable'=> false,
					'detailable'=> true,
					'pdf'		=> false,
					'xls'		=> false,
					'pagination'=> $limit,
					'filters'  	=> null,
					'toolbars'	=> null,
					'header'  	=> $header,
					'body'  	=> $body,
					'footer'  	=> null,
				)
			);
		}
		echo json_encode($this->data['list']);
	}
	
	public function m_form_user_info($user_id=null){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}
		
		if($user_id != null){	
			$r_alias = '';
			$r_email = '';
			$r_phone = '';
			$r_address = '';
			$r_photo = '';
			
			$filter = array();
			$filter[] = "USER_ID = '". $user_id. "'";
			$this->data['result'] = $this->user_info_model->get($filter);
			$fields = array();
			if (!empty($this->data['result'])){
				foreach($this->data['result'] as $value){
					$r_id 	= $value->ID;
					$r_alias = $value->ALIAS;
					$r_email = $value->EMAIL;
					$r_phone = $value->PHONE;
					$r_address = $value->ADDRESS;
					$r_photo = $value->PHOTO_1;
				}
				$fields[] = (object) array(
					'type' 		=> 'hidden',
					'label' 	=> 'id',
					'name' 		=> 'id',
					'value' 	=> $r_id,
					'classes' 	=> '',
				);				
			}

			$fields[] = (object) array(
				'type' 		=> 'hidden',
				'label' 	=> 'user_id',
				'name' 		=> 'user_id',
				'value' 	=> $user_id,
				'classes' 	=> '',
			);			
						
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'Alias',
				'name' 			=> 'alias',
				'placeholder'	=> 'user alias',
				'value' 		=> $r_alias,
				'classes' 		=> 'full-width',
			);
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'Email',
				'name' 			=> 'email',
				'placeholder'	=> 'email',
				'value' 		=> $r_email,
				'classes' 		=> '',
			);
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'Phone',
				'name' 			=> 'phone',
				'placeholder'	=> 'phone',
				'value' 		=> $r_phone,
				'classes' 		=> '',
			);
			$fields[] = (object) array(
				'type' 			=> 'textarea',
				'label' 		=> 'Address',
				'name' 			=> 'address',
				'placeholder'	=> 'address',
				'value' 		=> $r_address,
				'classes' 		=> 'full-width',
			);
			if (!empty($this->data['result'])){
				$fields[] = (object) array(
					'type' 			=> 'file',
					'label' 		=> 'Upload Photo',
					'name' 			=> 'photo',
					'placeholder'	=> 'Select Photo',
					'value' 		=> '',
					'classes' 		=> '',
				);				
				$fields[] = (object) array(
					'type' 			=> 'info',
					'label' 		=> 'Recent Photo',
					'value' 		=> "<img width='256px' alt='recent_photo' src='".$r_photo."'/>",
					'classes' 		=> '',
				);
				$fields[] = (object) array(
					'type' 			=> 'hidden',
					'label' 		=> 'Recent Photo',
					'name' 			=> 'recent_photo',
					'value' 		=> $r_photo,
					'classes' 		=> '',
				);				
			}else{
				$fields[] = (object) array(
					'type' 			=> 'file',
					'label' 		=> 'Upload Photo',
					'name' 			=> 'photo',
					'placeholder'	=> 'Select Photo',
					'value' 		=> '',
					'classes' 		=> 'full-width',
				);				
			}
			$this->data['output'] = (object) array (
				'type'  	=> 'modal_form',
				'data'		=> (object) array (
					'target'	=> site_url( __CLASS__ ).'/insert_user_info',
					'title'		=> 'User Information',
					'id'		=> 'modal-form-1',
					'fields'  	=> $fields,
					
				)
			);	
			echo json_encode($this->data['output']);
		}
	}
	
	public function insert_user_info(){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}
		$success_upload = false;
		$error_info = array();
		$error_status = false;
		if($_POST['alias'] == ''){
			$error_info[] = 'User Alias can not be null';
			$error_status = true;
		}
		if($_POST['email'] != '' or $_POST['email'] != null){
			if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
				$error_info[] = 'Wrong email format';
				$error_status = true;			
			}
		}
		if(isset($_FILES["photo"])){
			if($_FILES["photo"] != null){
				$allowed_exts = array("gif", "jpeg", "jpg", "png");
				$extension = explode("/", $_FILES["photo"]["type"]);
				$extension = end($extension);
				$true_photo = false;
				foreach($allowed_exts as $val){
					if($extension == $val){
						$true_photo = true;
					}
				}
				if($true_photo == false){
					$error_info[] = 'Only accept image';
					$error_status = true;				
				}
			}
			//upload file
			$filename = $_FILES['photo']['name'];
			$target_dir = FCPATH."public/photos/";
			$uniq = date('YmdHis');
			$rename = $uniq . '_' . $filename;
			$success_upload = move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir . $rename);
			
			if(!$success_upload){
				$error_info[] = 'Error upload photo';
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
			if($success_upload){
				$this->data['insert'] = array(
						'ALIAS' => $_POST['alias'],
						'EMAIL' => $_POST['email'],
						'PHONE' => $_POST['phone'],
						'ADDRESS' => $_POST['address'],
						'PHOTO_1' => base_url().'public/photos/'.$rename,
						'USER_ID' => $_POST['user_id'],
					);
				if(isset($_POST['recent_photo'])){
					if($_POST['recent_photo'] != null or $_POST['recent_photo'] != ''){
						$recent_photo = explode('/', $_POST['recent_photo']);
						$recent_photo = end($recent_photo);
						unlink(FCPATH."public/photos/".$recent_photo);
					}
				}
			}else{
				$this->data['insert'] = array(
						'ALIAS' => $_POST['alias'],
						'EMAIL' => $_POST['email'],
						'PHONE' => $_POST['phone'],
						'ADDRESS' => $_POST['address'],
						'USER_ID' => $_POST['user_id'],
					);					
			}					
			//var_dump($this->data['insert']);die;
			if(isset($_POST['id'])){
				if($_POST['id'] != null or $_POST['id'] != ''){
					$result = $this->user_info_model->update($this->data['insert'], $_POST['id']);
					$info = array();
					$info[] = 'Update data success';
					$this->data['success'] = (object) array (
						'type'  	=> 'success',
						'data'		=> (object) array (
							'info'	=> $info,
						)
					);						
				}
			}else{
				$result = $this->user_info_model->insert($this->data['insert']);
				$info = array();
				$info[] = 'Insert data success';
				$this->data['success'] = (object) array (
					'type'  	=> 'success',
					'data'		=> (object) array (
						'info'	=> $info,
					)
				);					
			}		
			echo json_encode($this->data['success']);				
		}		
	}
	
	public function m_user_info($id = null){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}
		if($id != null){
			$filters = array();
			$filters[] = "USER_ID = '". $id. "'";
			$data = $this->user_info_model->get($filters);
			
			$body= array();			
			if (empty($data)) {
                $body[] = array(
                    (object) array ('colspan' => 100, 'classes' => ' empty bold align-center', 'value' => 'No Data')
                );
			} else {
				foreach($data as $value){
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Profile Photo' ),					
						(object) array( 'classes' => ' align-left ', 'value' => "<img src='".$value->PHOTO_1."' />" ),
					);					
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Alias' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->ALIAS ),
					);
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Email' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->EMAIL ),
					);
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Phone' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->PHONE ),
					);
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Address' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->ADDRESS ),
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
				'type'  	=> 'modal_table',
				'data'		=> (object) array (
					'title'		=> 'Detail User Info',
					'id'		=> 'modal-table-2',
					'header'	=> $header,
					'body'		=> $body,
					'classes'	=> '',
				)
			);			
			echo json_encode($this->data['detail']);
		}
	}
	
	public function insert(){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}		
		if(isset($_POST['submit'])){
			//validation
			$error_info = array();
			$error_status = false;
			if($_POST['username'] == ''){
				$error_info[] = 'Username can not be null';
				$error_status = true;
			}
			if ( preg_match('/\s/', $_POST['username']) )	{
				$error_info[] = 'Username can not contain whitespace';
				$error_status = true;
			}	
			if(strlen ($_POST['username']) < 3){
				$error_info[] = 'Username minimum 3 character';
				$error_status = true;
			}		

			$filter = array();
			$filter[] = "A.USERNAME = '". $_POST['username']."'";
			$data = $this->user_model->get($filter);
			if(!empty($data)){
				$error_info[] = 'Username must be unique';
				$error_status = true;				
			}			
			if($_POST['password'] == ''){
				$error_info[] = 'Password can not be null';
				$error_status = true;
			}
			if ( preg_match('/\s/', $_POST['password']) )	{
				$error_info[] = 'Password can not contain whitespace';
				$error_status = true;
			}			
			if($_POST['password2'] == ''){
				$error_info[] = 'Password 2 can not be null';
				$error_status = true;
			}
			if($_POST['password'] != $_POST['password2']){
				$error_info[] = 'Field Password and Password 2 must be the same';
				$error_status = true;
			}
			if($_POST['role'] == ''){
				$error_info[] = 'Role can not be null';
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
				if($_POST['status'] != '' ){
					$this->data['insert'] = array(
							'USERNAME' => $_POST['username'],
							'PASSWORD' => md5($_POST['password']),
							'STATUS' => $_POST['status'],
							'ROLE_ID' => $_POST['role'],
						);
				}else{
					$this->data['insert'] = array(
							'USERNAME' => $_POST['username'],
							'PASSWORD' => md5($_POST['password']),
							'ROLE_ID' => $_POST['role'],
						);					
				}
				//var_dump($this->data['insert']);die;
				$result = $this->user_model->insert($this->data['insert']);
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
			$role = array();
			$filter = array();
			$filter[] = "STATUS ='1'";
			$data = $this->role_model->get($filter);
			
			if (empty($data)) {
				//$parent[] = (object) array('label'=>'No Data', 'value'=>'nodata');
			} else {
				foreach ($data as $value) {
					$role[] = (object) array('label'=>$value->ROLE_NAME, 'value'=>$value->ID);
				}
			}
			
			$status = array();
			$status[] = (object) array('label'=>'1 - ACTIVE', 'value'=>'1');
			$status[] = (object) array('label'=>'2 - INACTIVE', 'value'=>'0');
			$status[] = (object) array('label'=>'3 - NOT CONFIRM', 'value'=>'5');
				
			$fields = array();
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'Username',
				'placeholder' 	=> 'username',
				'name' 			=> 'username',
				'value' 		=> '',
				'classes' 		=> 'full-width',
			);
			$fields[] = (object) array(
				'type' 			=> 'password',
				'label' 		=> 'Password',
				'placeholder' 	=> 'Password',
				'name' 			=> 'password',
				'value' 		=> '',
				'classes' 		=> '',
			);			
			$fields[] = (object) array(
				'type' 			=> 'password',
				'label' 		=> 'Password 2',
				'placeholder' 	=> 'Password 2',
				'name' 			=> 'password2',
				'value' 		=> '',
				'classes' 		=> '',
			);			
			$fields[] = (object) array(
				'type' 			=> 'select',
				'label' 		=> 'Status',
				'placeholder' 	=> '--Select status--',
				'name' 			=> 'status',
				'value' 		=> '',
				'options'		=> $status,
				'classes' 		=> '',
			);			
			$fields[] = (object) array(
				'type' 			=> 'select',
				'label' 		=> 'Role',
				'name' 			=> 'role',
				'placeholder'	=> '--Select Role--',
				'value' 		=> '',
				'options'		=> $role,
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
			if(isset($_POST['username'])){
				if($_POST['username'] == ''){
					$error_info[] = 'Username can not be null';
					$error_status = true;
				}
				if ( preg_match('/\s/', $_POST['username']) )	{
					$error_info[] = 'Username can not contain whitespace';
					$error_status = true;
				}	
				if(strlen ($_POST['username']) < 3){
					$error_info[] = 'Username minimum 3 character';
					$error_status = true;
				}
				$filter = array();
				$filter[] = "A.USERNAME = '". $_POST['username']."'";
				$filter[] = "A.id != '". $_POST['id']."'";
				$data = $this->user_model->get($filter);			
				if(!empty($data)){
					$error_info[] = 'Username must be unique';
					$error_status = true;				
				}
			}
			
			if($_POST['password'] == ''){
				$error_info[] = 'Password can not be null';
				$error_status = true;
			}
			if ( preg_match('/\s/', $_POST['password']) )	{
				$error_info[] = 'Password can not contain whitespace';
				$error_status = true;
			}			
			if($_POST['password2'] == ''){
				$error_info[] = 'Password 2 can not be null';
				$error_status = true;
			}
			if($_POST['password'] != $_POST['password2']){
				$error_info[] = 'Field Password and Password 2 must be the same';
				$error_status = true;
			}
			if(isset($_POST['role']) AND $_POST['role'] == ''){
				$error_info[] = 'Role can not be null';
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
				if($this->session->userdata('ROLE_NAME') == 'administrator'){
					if(isset($_POST['status']) and $_POST['status'] != '' ){
						$this->data['update'] = array(
								'USERNAME' => $_POST['username'],
								'PASSWORD' => md5($_POST['password']),
								'STATUS' => $_POST['status'],
								'ROLE_ID' => $_POST['role'],
							);
					}else{
						$this->data['update'] = array(
								'USERNAME' => $_POST['username'],
								'PASSWORD' => md5($_POST['password']),
								'ROLE_ID' => $_POST['role'],
							);					
					}
				}else{
					$this->data['update'] = array(
							'PASSWORD' => md5($_POST['password']),
						);						
				}
				//var_dump($this->data['insert']);die;
				$result = $this->user_model->update($this->data['update'], $_POST['id']);
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
			$r_username = '';
			$r_password = '';
			$r_password2 = '';
			$r_status = '';
			$r_role = '';
			
			$filter = array();
			$filter[] = "A.ID = ". $_POST['id'];
			$this->data['result'] = $this->user_model->get($filter);
			//var_dump($this->data['result']);
			foreach($this->data['result'] as $value){
				$r_id 	= $value->ID;
				$r_username = $value->USERNAME;
				$r_password = $value->PASSWORD;
				$r_password2 = $value->PASSWORD;
				$r_status = $value->STATUS;
				$r_role = $value->ROLE_ID;
			}
			
			$role = array();
			$filter = array();
			$filter[] = "STATUS ='1'";
			$data = $this->role_model->get($filter);
			
			if (empty($data)) {
				
			} else {
				foreach ($data as $value) {
					$role[] = (object) array('label'=>$value->ROLE_NAME, 'value'=>$value->ID);
				}
			}
			
			$status = array();
			$status[] = (object) array('label'=>'1 - ACTIVE', 'value'=>'1');
			$status[] = (object) array('label'=>'2 - INACTIVE', 'value'=>'0');
			$status[] = (object) array('label'=>'3 - NOT CONFIRM', 'value'=>'5');
				
			$fields = array();
			$fields[] = (object) array(
				'type' 		=> 'hidden',
				'label' 	=> 'id',
				'name' 		=> 'id',
				'value' 	=> $r_id,
				'classes' 	=> '',
			);			
			if($this->session->userdata('ROLE_NAME') == 'administrator'){
				$fields[] = (object) array(
					'type' 			=> 'text',
					'label' 		=> 'Username',
					'placeholder' 	=> 'username',
					'name' 			=> 'username',
					'value' 		=> $r_username,
					'classes' 		=> 'full-width',
				);
				$fields[] = (object) array(
					'type' 			=> 'password',
					'label' 		=> 'Password',
					'placeholder' 	=> 'Password',
					'name' 			=> 'password',
					'value' 		=> $r_password,
					'classes' 		=> '',
				);			
				$fields[] = (object) array(
					'type' 			=> 'password',
					'label' 		=> 'Password 2',
					'placeholder' 	=> 'Password 2',
					'name' 			=> 'password2',
					'value' 		=> $r_password2,
					'classes' 		=> '',
				);			
				$fields[] = (object) array(
					'type' 			=> 'select',
					'label' 		=> 'Status',
					'placeholder' 	=> '--Select status--',
					'name' 			=> 'status',
					'value' 		=> $r_status,
					'options'		=> $status,
					'classes' 		=> '',
				);			
				$fields[] = (object) array(
					'type' 			=> 'select',
					'label' 		=> 'Role',
					'name' 			=> 'role',
					'placeholder'	=> '--Select Role--',
					'value' 		=> $r_role,
					'options'		=> $role,
					'classes' 		=> '',
				);
			}else{
				$fields[] = (object) array(
					'type' 			=> 'password',
					'label' 		=> 'Password',
					'placeholder' 	=> 'Password',
					'name' 			=> 'password',
					'value' 		=> $r_password,
					'classes' 		=> '',
				);			
				$fields[] = (object) array(
					'type' 			=> 'password',
					'label' 		=> 'Password 2',
					'placeholder' 	=> 'Password 2',
					'name' 			=> 'password2',
					'value' 		=> $r_password2,
					'classes' 		=> '',
				);					
			}

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
			$data = $this->user_model->get($filters);
			
			$body= array();			
			if (empty($data)) {
                $body[] = array(
                    (object) array ('colspan' => 100, 'classes' => ' empty bold align-center', 'value' => 'No Data')
                );
			} else {
				foreach($data as $value){
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Username' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->USERNAME ),
					);
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Role' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->ROLE_NAME ),
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
			
			$result = $this->user_model->get($filters);
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
				
			$result = $this->user_model->update($this->data['update'], $_POST['id']);
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
		$result = $this->user_model->delete($this->data['delete']);
		
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

