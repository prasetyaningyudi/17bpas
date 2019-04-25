<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sampletwotable extends CI_Controller {
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
		$filters1 = array();
		$limit1 = array('10', '0');
		$r_nama = '';
		$r_parent = '';
		$r_order = '';
		$r_status = '';
		
		$filters2 = array();
		$limit2 = array('10', '0');		
		$r_menuname = '';
		$r_rolename = '';		

		//var_dump($_POST['nama']);
		if(isset($_POST['submit1'])){
			if (isset($_POST['nama'])) {
				if ($_POST['nama'] != '' or $_POST['nama'] != null) {
					$filters1[] = "A.MENU_NAME LIKE '%" . $_POST['nama'] . "%'";
					$r_nama = $_POST['nama'];
				}
			}
			if (isset($_POST['parent'])) {
				if ($_POST['parent'] != '' or $_POST['parent'] != null) {
					$filters1[] = "A.MENU_ID = '" . $_POST['parent'] . "'";
					$r_parent = $_POST['parent'];
				}
			}
			if (isset($_POST['status'])) {
				if ($_POST['status'] != '' or $_POST['status'] != null) {
					$filters1[] = "A.STATUS = '" . $_POST['status'] . "'";
					$r_status = $_POST['status'];
				}
			}
			if (isset($_POST['order'])) {
				if ($_POST['order'] != '' or $_POST['order'] != null) {
					$filters1[] = "A.MENU_ORDER LIKE '" . $_POST['order'] . "%'";
					$r_order = $_POST['order'];
				}
			}			
			if (isset($_POST['offset1'])) {
				if ($_POST['offset1'] != '' or $_POST['offset1'] != null) {
					$limit1[1] = $_POST['offset1'];
				}
			}			
	
		}
		
		if(isset($_POST['submit2'])){
			if (isset($_POST['menu_name'])) {
				if ($_POST['menu_name'] != '' or $_POST['menu_name'] != null) {
					$filters2[] = "A.MENU_ID = '" . $_POST['menu_name'] . "'";
					$r_menuname = $_POST['menu_name'];
				}
			}
			if (isset($_POST['role_name'])) {
				if ($_POST['role_name'] != '' or $_POST['role_name'] != null) {
					$filters2[] = "A.ROLE_ID = '" . $_POST['role_name'] . "'";
					$r_rolename = $_POST['role_name'];
				}
			}
			if (isset($_POST['offset2'])) {
				if ($_POST['offset2'] != '' or $_POST['offset2'] != null) {
					$limit2[1] = $_POST['offset2'];
				}
			}			
		}		
		
		$data1 = $this->menu_model->get($filters1, $limit1);
		//var_dump($data1);
		//die;
		$total_data1 = count($this->menu_model->get($filters1));
		$limit1[] = $total_data1;			
		
		$data2 = $this->assignmenu_model->get($filters2, $limit2);
		$total_data2 = count($this->assignmenu_model->get($filters2));
		$limit2[] = $total_data2;		
		
		
		//var_dump($data);

		$no_body1 = 0;
		$body1= array();
		if(isset($data1)){
            if (empty($data1)) {
                $body1[$no_body1] = array(
                    (object) array ('colspan' => 100, 'classes' => ' empty bold align-center', 'value' => 'No Data')
                );
			} else {
				foreach ($data1 as $value) {
					if($value->BMENU_NAME == null){
						$menu_name = $value->MENU_NAME;
					}else{
						$menu_name = '&nbsp;&nbsp;&nbsp;'.$value->MENU_NAME;
					}
					
					$body1[$no_body1] = array(
						(object) array( 'classes' => ' hidden ', 'value' => $value->ID ),
						(object) array( 'classes' => ' bold align-center ', 'value' => $no_body1+1 ),
						(object) array( 'classes' => ' align-left ', 'value' => $menu_name ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->PERMALINK ),
						(object) array( 'classes' => ' align-center ', 'value' => '<i class="fa fa-'.$value->MENU_ICON.'"></i>' ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->MENU_ORDER ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->BMENU_NAME ),
						(object) array( 'classes' => ' align-center ', 'value' => $value->STATUS ),
					);
					$no_body1++;
				}
			}
        } else {
            $body1[$no_body1] = array(
                (object) array ('colspan' => 100, 'classes' => ' empty bold align-center', 'value' => 'Filter First')
            );
        }
		
		$header1 = array(
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
		$data1 = $this->menu_model->get_parent();
		
		if (empty($data1)) {
			
		} else {
			foreach ($data1 as $value) {
				$parent[] = (object) array('label'=>$value->MENU_NAME, 'value'=>$value->ID);
			}
		}	
			
		$fields1 = array();
		$fields1[] = (object) array(
			'type' 			=> 'text',
			'label' 		=> 'Name',
			'placeholder' 	=> 'Menu Name',
			'name' 			=> 'nama',
			'value' 		=> $r_nama,
			'classes' 		=> 'full-width',
		);
		$fields1[] = (object) array(
			'type' 			=> 'text',
			'label' 		=> 'Menu Order',
			'name' 			=> 'order',
			'placeholder'	=> 'Input order like',
			'value' 		=> $r_order,
			'classes' 		=> 'full-width',
		);			
		$fields1[] = (object) array(
			'type' 			=> 'text',
			'label' 		=> 'Status',
			'name' 			=> 'status',
			'placeholder'	=> 'Input status',
			'value' 		=> $r_status,
			'classes' 		=> 'full-width',
		);			
		$fields1[] = (object) array(
			'type' 			=> 'select',
			'label' 		=> 'Parent menu',
			'name' 			=> 'parent',
			'placeholder'	=> '--Select Parent--',
			'value' 		=> $r_parent,
			'options'		=> $parent,
			'classes' 		=> 'required full-width',
		);
		
		$no_body2 = 0;
		$body2= array();
		if(isset($data2)){
            if (empty($data2)) {
                $body2[$no_body2] = array(
                    (object) array ('colspan' => 100, 'classes' => ' empty bold align-center', 'value' => 'No Data')
                );
			} else {
				foreach ($data2 as $value) {
					$body2[$no_body2] = array(
						(object) array( 'classes' => ' hidden ', 'value' => $value->ID ),
						(object) array( 'classes' => ' bold align-left ', 'value' => $no_body2+1 ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->MENU_ID ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->ROLE_ID ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->MENU_NAME ),
						(object) array( 'classes' => ' align-left ', 'value' => $value->ROLE_NAME ),
					);
					$no_body2++;
				}
			}
        } else {
            $body2[$no_body2] = array(
                (object) array ('colspan' => 100, 'classes' => ' empty bold align-center', 'value' => '')
            );
        }
		
		$header2 = array(
			array (
				(object) array ('rowspan' => 1, 'classes' => 'bold align-left capitalize', 'value' => 'No'),
				(object) array ('colspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'menu id'),					
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'role id'),			
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'menu name'),			
				(object) array ('rowspan' => 1, 'classes' => 'bold align-center capitalize', 'value' => 'role name'),		
			)		
		);

		$menu_name = array();
		$filter2 = array();
		$filter2[] = " A.STATUS = '1'";
		$data2 = $this->menu_model->get($filter2);
		if (empty($data2)) {
			
		} else {
			foreach ($data2 as $value) {
				$menu_name[] = (object) array('label'=>$value->MENU_NAME, 'value'=>$value->ID);
			}
		}	
		
		$role_name = array();
		$filter2 = array();
		$filter2[] = " STATUS = '1'";
		$data2 = $this->role_model->get($filter2);
		if (empty($data2)) {
			
		} else {
			foreach ($data2 as $value) {
				$role_name[] = (object) array('label'=>$value->ROLE_NAME, 'value'=>$value->ID);
			}
		}		
			
		$fields2 = array();
		$fields2[] = (object) array(
			'type' 			=> 'select',
			'label' 		=> 'Menu name',
			'name' 			=> 'menu name',
			'placeholder'	=> '--Select Menu--',
			'value' 		=> $r_menuname,
			'options'		=> $menu_name,
			'classes' 		=> 'full-width
			',
		);			
		$fields2[] = (object) array(
			'type' 			=> 'select',
			'label' 		=> 'Role Name',
			'name' 			=> 'role_name',
			'placeholder'	=> '--Select Role--',
			'value' 		=> $r_rolename,
			'options'		=> $role_name,
			'classes' 		=> 'full-width',
		);			
		
		$this->data['list'] = (object) array (
			'type'  	=> 'table_two',
			'data'		=> (object) array (
				'classes'  	=> 'striped bordered hover',
				'insertable'=> false,
				'editable'	=> false,
				'deletable'	=> false,
				'statusable'=> false,
				'detailable'=> false,
				'pdf'		=> false,
				'xls'		=> false,
				'pagination'=> $limit1,
				'toolbars'	=> null,
				'table1'	=> (object) array (
					'subtitle'	=> 'Title tab 1',
					'filters'  	=> $fields1,				
					'header'  	=> $header1,
					'body'  	=> $body1,
					'footer'  	=> null,				
				),
				'table2'	=> (object) array (
					'subtitle'	=> 'Title tab 2',
					'filters'  	=> $fields2,					
					'header'  	=> $header2,
					'body'  	=> $body2,
					'footer'  	=> null,				
				),				
				'filterfirst'=> false,
			)
		);
		echo json_encode($this->data['list']);
	}
	
}

