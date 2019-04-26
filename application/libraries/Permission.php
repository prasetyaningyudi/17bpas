<?php

$roles = array(
			'' => array (
				'home' => array ('index', 'lists'),	
				'pembimbingan' => array ('index', 'lists'),	
				'pengajuan' => array ('index', 'lists', 'insert'),					
			),
			'administrator' => array (
				'home' => array ('index', 'lists'),				
				'assignmenu' => array ('index', 'lists', 'insert', 'update', 'delete'),
				'menu' => array ('index', 'lists', 'insert', 'update', 'update_status', 'delete', 'detail', 'modal_form', 'modal_table', 'data_form'),
				'user' => array ('index', 'lists', 'insert', 'update', 'update_status', 'delete', 'detail', 'm_form_user_info', 'insert_user_info', 'm_user_info'),
				'role' => array ('index', 'lists', 'insert', 'update', 'update_status', 'detail'),
				'app_data' => array ('index', 'lists', 'insert', 'update', 'update_status', 'delete', 'detail'),							
				'sample' => array ('index', 'lists', 'insert', 'update', 'delete', 'detail'),		
				'litmas' => array ('index', 'lists', 'insert', 'update', 'update_status', 'delete', 'detail'),		
				'pembimbingan' => array ('index', 'lists', 'insert', 'update', 'delete', 'detail'),			
				'pengajuan' => array ('index', 'lists', 'insert', 'update', 'delete', 'detail'),			
			),
			'operator' => array (
				'home' => array ('index', 'lists'),
				'user' => array ('index', 'lists', 'update','detail', 'm_form_user_info', 'insert_user_info', 'm_user_info'),
				'litmas' => array ('index', 'lists', 'insert', 'update', 'update_status', 'delete', 'detail'),		
				'pembimbingan' => array ('index', 'lists', 'insert', 'update', 'delete', 'detail'),				
			),				
		);


