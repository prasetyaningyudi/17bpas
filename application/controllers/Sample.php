<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sample extends CI_Controller {
	private $data;
	
	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->library('auth');			
		$this->load->helper('url');			
		$this->load->database();
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
		$this->data['title'] = 'Menu';
	}

	public function index(){
		if($this->auth->get_permission($this->session->userdata('ROLE_NAME'), __CLASS__ , __FUNCTION__ ) == false){
			redirect ('authentication/unauthorized');
		}		
		$this->data['subtitle'] = 'Setup';
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
		$limit = array('1', '0');
		$r_nama = '';
		$r_parent = '';
		$r_order = '';
		$r_status = '';

		//var_dump($_POST['nama']);
		if(isset($_POST['submit'])){
			if (isset($_POST['nama'])) {
				if ($_POST['nama'] != '' or $_POST['nama'] != null) {
					$filters[] = "A.MENU_NAME LIKE '%" . $_POST['nama'] . "%'";
					$r_nama = $_POST['nama'];
				}
			}
			if (isset($_POST['parent'])) {
				if ($_POST['parent'] != '' or $_POST['parent'] != null) {
					$filters[] = "A.MENU_ID = '" . $_POST['parent'] . "'";
					$r_parent = $_POST['parent'];
				}
			}
			if (isset($_POST['status'])) {
				if ($_POST['status'] != '' or $_POST['status'] != null) {
					$filters[] = "A.STATUS = '" . $_POST['status'] . "'";
					$r_status = $_POST['status'];
				}
			}
			if (isset($_POST['order'])) {
				if ($_POST['order'] != '' or $_POST['order'] != null) {
					$filters[] = "A.MENU_ORDER LIKE '" . $_POST['order'] . "%'";
					$r_order = $_POST['order'];
				}
			}			
			if (isset($_POST['offset'])) {
				if ($_POST['offset'] != '' or $_POST['offset'] != null) {
					$limit[1] = $_POST['offset'];
				}
			}			
			$data = $this->menu_model->get($filters, $limit);
			//var_dump($data);
			$total_data = count($this->menu_model->get($filters));
			$limit[] = $total_data;		
		}
		
		
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
					if($value->BMENU_NAME == null){
						$menu_name = $value->MENU_NAME;
					}else{
						$menu_name = '&nbsp;&nbsp;&nbsp;'.$value->MENU_NAME;
					}
					
					$body[$no_body] = array(
						(object) array( 'classes' => ' hidden ', 'value' => $value->ID ),
						(object) array( 'classes' => ' bold align-center ', 'value' => $no_body+1 ),
						(object) array( 'classes' => ' align-left ', 'value' => $menu_name ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->PERMALINK ),
						(object) array( 'classes' => ' align-center ', 'value' => '<i class="fa fa-'.$value->MENU_ICON.'"></i>' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->MENU_ORDER ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->BMENU_NAME ),
						(object) array( 'classes' => ' align-center ', 'value' => $value->STATUS ),
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
				(object) array ('colspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'name'),					
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'permalink'),			
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'icon'),			
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'order'),			
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'parent'),			
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'status'),			
			)		
		);

		$parent = array();
		$data = $this->menu_model->get_parent();
		
		if (empty($data)) {
			
		} else {
			foreach ($data as $value) {
				$parent[] = (object) array('label'=>$value->MENU_NAME, 'value'=>$value->ID);
			}
		}	
			
		$fields = array();
		$fields[] = (object) array(
			'type' 			=> 'text',
			'label' 		=> 'Name',
			'placeholder' 	=> 'Menu Name',
			'name' 			=> 'nama',
			'value' 		=> $r_nama,
			'classes' 		=> 'full-width',
		);
		$fields[] = (object) array(
			'type' 			=> 'text',
			'label' 		=> 'Menu Order',
			'name' 			=> 'order',
			'placeholder'	=> 'Input order like',
			'value' 		=> $r_order,
			'classes' 		=> 'full-width',
		);			
		$fields[] = (object) array(
			'type' 			=> 'text',
			'label' 		=> 'Status',
			'name' 			=> 'status',
			'placeholder'	=> 'Input status',
			'value' 		=> $r_status,
			'classes' 		=> 'full-width',
		);			
		$fields[] = (object) array(
			'type' 			=> 'select',
			'label' 		=> 'Parent menu',
			'name' 			=> 'parent',
			'placeholder'	=> '--Select Parent--',
			'value' 		=> $r_parent,
			'options'		=> $parent,
			'classes' 		=> 'required full-width',
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
				'xls'		=> false,
				'pagination'=> $limit,
				'filters'  	=> $fields,
				'toolbars'	=> null,
				'header'  	=> $header,
				'body'  	=> $body,
				'footer'  	=> null,
				'filterfirst'=> true,
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
			if($_POST['name'] == ''){
				$error_info[] = 'Name can not be null';
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
					'NAME' => $_POST['name'],
					'ICON' => $_POST['icon'],
					'FAVICON' => $_POST['favicon'],
					'NOTES' => $_POST['notes'],
					'USER_ID' => $this->session->userdata('ID'),
				);	
				//var_dump($this->data['insert']);die;
				$result = $this->app_data_model->insert($this->data['insert']);
				$info = array();
				$info[] = 'Insert data success';
				$info[] = 'Please go homepage to see the changes';
				$this->data['success'] = (object) array (
					'type'  	=> 'success',
					'data'		=> (object) array (
						'info'	=> $info,
					)
				);			
				echo json_encode($this->data['success']);				
			}
		}else{
			$jenis_kelamin = array();
			$jenis_kelamin[] = (object) array('label'=>'Laki-laki', 'value'=>'Laki-laki');
			$jenis_kelamin[] = (object) array('label'=>'Perempuan', 'value'=>'Perempuan');
			$jenis_kelamin[] = (object) array('label'=>'Tidak Jelas', 'value'=>'Tidak Jelas');
			
			$status = array();
			$status[] = (object) array('label'=>'lajang', 'value'=>'lajang');
			$status[] = (object) array('label'=>'menikah', 'value'=>'menikah');
			$status[] = (object) array('label'=>'menikah punya anak', 'value'=>'menikah punya anak');	

			$bekerja = array();
			$bekerja[] = (object) array('label'=>'bekerja', 'value'=>'bekerja');
			$bekerja[] = (object) array('label'=>'tidak bekerja', 'value'=>'tidak bekerja');
			$bekerja[] = (object) array('label'=>'sekolah', 'value'=>'sekolah');			

			$penyakit = array();
			$penyakit[] = (object) array('label'=>'pernah', 'value'=>'pernah');
			$penyakit[] = (object) array('label'=>'tidak pernah', 'value'=>'tidak pernah');		
			
			$fields = array();
			$fields[] = (object) array(
				'type' 			=> 'hidden',
				'name' 			=> 'id',
				'value' 		=> '',
				'classes' 		=> '',
			);
			$fields[] = (object) array(
				'type' 			=> 'label',
				'label' 		=> 'Contoh label',
				'classes' 		=> 'full-width',
			);			
			$fields[] = (object) array(
				'type' 			=> 'toogle',
				'label' 		=> 'Toogle1',
				'name' 			=> 'toogle1',
				'value' 		=> '',
				'classes' 		=> '',
			);	
			$fields[] = (object) array(
				'type' 			=> 'toogle',
				'label' 		=> 'Toogle2',
				'name' 			=> 'toogle2',
				'value' 		=> '',
				'classes' 		=> '',
			);	
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'Text toogle 1',
				'name' 			=> 'text-toogle1',
				'placeholder'	=> 'Text toogle 1',
				'value' 		=> '',
				'classes' 		=> 'active-when-toogle1-on',
			);	
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'Text toogle 2',
				'name' 			=> 'text-toogle2',
				'placeholder'	=> 'Text toogle 2',
				'value' 		=> '',
				'classes' 		=> 'active-when-toogle2-on',
			);				
			$fields[] = (object) array(
				'type' 			=> 'daterange',
				'label' 		=> 'date range',
				'format' 		=> 'YYYY-MM-DD',
				'name' 			=> 'daterange',
				'value' 		=> '',
				'classes' 		=> '',
			);	
			$fields[] = (object) array(
				'type' 			=> 'date',
				'label' 		=> 'tanggal',
				'format' 		=> 'YYYY-MM-DD',
				'name' 			=> 'date',
				'value' 		=> '',
				'classes' 		=> '',
			);				
			$fields[] = (object) array(
				'type' 			=> 'radio',
				'label' 		=> 'Jenis Kelamin',
				'name' 			=> 'jk',
				'placeholder'	=> 'jk',
				'options'		=> $jenis_kelamin,
				'value' 		=> '',
				'classes' 		=> '',
			);	
			$fields[] = (object) array(
				'type' 			=> 'checkbox',
				'label' 		=> 'Status',
				'name' 			=> 'status',
				'placeholder'	=> 'status',
				'options'		=> $status,
				'value' 		=> '',
				'classes' 		=> '',
			);
			$fields[] = (object) array(
				'type' 			=> 'select',
				'label' 		=> 'bekerja',
				'name' 			=> 'bekerja',
				'placeholder'	=> '--Pilih Bekerja--',
				'options'		=> $bekerja,
				'value' 		=> '',
				'classes' 		=> '',
			);
			$fields[] = (object) array(
				'type' 			=> 'blank',
				'classes' 		=> '',
			);			
			$fields[] = (object) array(
				'type' 			=> 'select',
				'label' 		=> 'penyakit',
				'name' 			=> 'penyakit',
				'placeholder'	=> '--Pilih Riwayat Penyakit--',
				'options'		=> $penyakit,
				'value' 		=> '',
				'classes' 		=> '',
			);	
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'Nama Penyakit',
				'name' 			=> 'nama-penyakit',
				'placeholder'	=> 'nama penyakit',
				'value' 		=> '',
				'classes' 		=> 'active-when-penyakit-pernah',
			);			
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'Nama',
				'name' 			=> 'nama',
				'placeholder'	=> 'nama',
				'value' 		=> '',
				'classes' 		=> 'full-width',
			);
			$fields[] = (object) array(
				'type' 			=> 'number',
				'label' 		=> 'No KTP',
				'name' 			=> 'ktp',
				'placeholder'	=> 'ktp',
				'value' 		=> '',
				'classes' 		=> 'full-width',
			);	
			$fields[] = (object) array(
				'type' 			=> 'label',
				'label' 		=> 'Contoh label',
				'classes' 		=> 'full-width',
			);	
			
			$fields[] = (object) array(
				'type' 			=> 'textarea',
				'label' 		=> 'Alamat',
				'name' 			=> 'alamat',
				'placeholder'	=> 'alamat',
				'value' 		=> '',
				'classes' 		=> 'full-width',
			);			
			$fields[] = (object) array(
				'type' 			=> 'separation',
				'classes' 		=> 'full-width',
			);
			$fields[] = (object) array(
				'type' 			=> 'email',
				'label' 		=> 'email',
				'name' 			=> 'email',
				'placeholder'	=> 'email',
				'value' 		=> '',
				'classes' 		=> '',
			);	
			$fields[] = (object) array(
				'type' 			=> 'password',
				'label' 		=> 'password',
				'name' 			=> 'password',
				'placeholder'	=> 'password',
				'value' 		=> '',
				'classes' 		=> '',
			);	
			$fields[] = (object) array(
				'type' 			=> 'file',
				'label' 		=> 'file',
				'name' 			=> 'file',
				'placeholder'	=> 'file',
				'value' 		=> '',
				'classes' 		=> '',
			);			
			$fields[] = (object) array(
				'type' 			=> 'info',
				'label' 		=> 'info',
				'name' 			=> 'info',
				'value' 		=> 'info value',
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
			if($_POST['name'] == ''){
				$error_info[] = 'Name can not be null';
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
						'NAME' => $_POST['name'],
						'ICON' => $_POST['icon'],
						'FAVICON' => $_POST['favicon'],
						'NOTES' => $_POST['notes'],
					);				
				$result = $this->app_data_model->update($this->data['update'], $_POST['id']);
				if($result == true){
					$info = array();
					$info[] = 'Update data successfully';
					$info[] = 'Please go homepage to see the changes';					
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
			$r_name = '';
			$r_icon = '';
			$r_favicon = '';
			$r_notes = '';
			
			$filter = array();
			$filter[] = "ID = ". $_POST['id'];
			$this->data['result'] = $this->app_data_model->get($filter);
			foreach($this->data['result'] as $value){
				$r_id 	= $value->ID;
				$r_name = $value->NAME;
				$r_icon = $value->ICON;
				$r_favicon = $value->FAVICON;
				$r_notes = $value->NOTES;
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
				'type' 			=> 'text',
				'label' 		=> 'App Name',
				'name' 			=> 'name',
				'placeholder'	=> 'name',
				'value' 		=> $r_name,
				'classes' 		=> 'full-width',
			);				
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'App Icon',
				'name' 			=> 'icon',
				'placeholder'	=> 'user icon from fontawesome',
				'value' 		=> $r_icon,
				'classes' 		=> 'full-width',
			);
			$fields[] = (object) array(
				'type' 			=> 'text',
				'label' 		=> 'App Favicon',
				'name' 			=> 'favicon',
				'placeholder'	=> 'use valid favicon link image png or ico, example:asset/images/favicon.png',
				'value' 		=> $r_favicon,
				'classes' 		=> 'full-width',
			);			
			$fields[] = (object) array(
				'type' 			=> 'textarea',
				'label' 		=> 'Marquee Text',
				'name' 			=> 'notes',
				'placeholder'	=> 'running on top navbar',
				'value' 		=> $r_notes,
				'classes' 		=> 'full-width',
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
			$filters[] = "ID = ". $_POST['id'];
			$data = $this->app_data_model->get($filters);
			
			$body= array();			
			if (empty($data)) {
                $body[] = array(
                    (object) array ('colspan' => 100, 'classes' => ' empty bold align-center', 'value' => 'No Data')
                );
			} else {
				foreach($data as $value){
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'App Name' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->NAME ),
					);
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'App Icon' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->ICON ),
					);
					$body[] = array(
						(object) array( 'classes' => ' bold align-left ', 'value' => 'Marquee Text' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->NOTES ),
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
			$filters[] = "ID = ". $_POST['id'];
			
			$result = $this->app_data_model->get($filters);
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
				
			$result = $this->app_data_model->update($this->data['update'], $_POST['id']);
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
		$result = $this->app_data_model->delete($this->data['delete']);
		
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

