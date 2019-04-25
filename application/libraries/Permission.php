<?php

$roles = array(
			'' => array (
		
			),
			'administrator' => array (
				'home' => array ('index', 'list'),				
				'assignmenu' => array ('index', 'lists', 'insert', 'update', 'delete'),
				'menu' => array ('index', 'lists', 'insert', 'update', 'update_status', 'delete', 'detail', 'modal_form', 'modal_table', 'data_form'),
				'user' => array ('index', 'lists', 'insert', 'update', 'update_status', 'delete', 'detail', 'm_form_user_info', 'insert_user_info', 'm_user_info'),
				'role' => array ('index', 'lists', 'insert', 'update', 'update_status', 'detail'),
				'app_data' => array ('index', 'lists', 'insert', 'update', 'update_status', 'delete', 'detail'),							
				'sample' => array ('index', 'lists', 'insert', 'update', 'delete', 'detail'),
				'realisasibelanja' => array ('index', 'lists'),	
				'sampletwotable' => array ('index', 'lists'),
				'cikpa' => array ('index', 'lists'),	
				'cik_salahspm' => array ('index', 'lists'),	
				'cik_penlpj' => array ('index', 'lists'),
				'cik_penkontrak' => array ('index', 'lists'),
				'cik_kelolaup' => array ('index', 'lists'),	
				'cik_retursp2d' => array ('index', 'lists'),
				'cik_hal3dipa' => array ('index', 'lists'),
				'cik_pentagihan' => array ('index', 'lists'),
				'cik_renkas' => array ('index', 'lists'),
				'cpendapatan' => array ('index', 'lists'),
				'cpendapatan_detil' => array ('index', 'lists'),
				'cpotongan' => array ('index', 'lists'),
				'cik_capaian_persatker' => array ('index', 'lists'),
				'cik_capaianikpa' => array ('index'),			
			),
			'pimpinan' => array (
				'home' => array ('index', 'list'),				
				'assignmenu' => array ('index', 'lists', 'insert', 'update', 'delete'),
				'menu' => array ('index', 'lists', 'insert', 'update', 'update_status', 'delete', 'detail', 'modal_form', 'modal_table', 'data_form'),
				'user' => array ('index', 'lists', 'insert', 'update', 'update_status', 'delete', 'detail', 'm_form_user_info', 'insert_user_info', 'm_user_info'),
				'role' => array ('index', 'lists', 'insert', 'update', 'update_status', 'detail'),
				'app_data' => array ('index', 'lists', 'insert', 'update', 'update_status', 'delete', 'detail'),							
				'sample' => array ('index', 'lists', 'insert', 'update', 'delete', 'detail'),
				'realisasibelanja' => array ('index', 'lists'),	
				'sampletwotable' => array ('index', 'lists'),
				'cikpa' => array ('index', 'lists'),	
				'cik_salahspm' => array ('index', 'lists'),	
				'cik_penlpj' => array ('index', 'lists'),
				'cik_penkontrak' => array ('index', 'lists'),
				'cik_kelolaup' => array ('index', 'lists'),	
				'cik_retursp2d' => array ('index', 'lists'),
				'cik_hal3dipa' => array ('index', 'lists'),
				'cik_pentagihan' => array ('index', 'lists'),
				'cik_renkas' => array ('index', 'lists'),
				'cpendapatan' => array ('index', 'lists'),
				'cpendapatan_detil' => array ('index', 'lists'),
				'cpotongan' => array ('index', 'lists'),
				'cik_capaian_persatker' => array ('index', 'lists'),
				'cik_capaianikpa' => array ('index'),			
			),
			'supervisor' => array (
				'user' => array ('index', 'list', 'insert', 'update', 'update_status', 'delete', 'detail', 'm_form_user_info', 'insert_user_info', 'm_user_info'),		
			),
			'operator' => array (
				'user' => array ('index', 'list', 'insert', 'update', 'update_status', 'delete', 'detail', 'm_form_user_info', 'insert_user_info', 'm_user_info'),
			),	
			'kppn' => array (
				'user' => array ('index', 'list', 'insert', 'update', 'update_status', 'delete', 'detail', 'm_form_user_info', 'insert_user_info', 'm_user_info'),
				'laporan' => array ('index', 'list', 'insert', 'update', 'detail'),
			),
			'kanwil' => array (
				'user' => array ('index', 'list', 'insert', 'update', 'update_status', 'delete', 'detail', 'm_form_user_info', 'insert_user_info', 'm_user_info'),
				'laporan' => array ('index', 'list', 'detail', 'm_form_edit_status', 'edit_status'),
			),			
		);


