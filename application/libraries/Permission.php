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
				'litmas' => array ('index', 'lists', 'insert', 'update', 'update_status', 'delete', 'detail'),		
				'pembimbingan' => array ('index', 'lists', 'insert', 'update', 'delete', 'detail'),		
			),
			'supervisor' => array (
				'user' => array ('index', 'list', 'insert', 'update', 'update_status', 'delete', 'detail', 'm_form_user_info', 'insert_user_info', 'm_user_info'),		
			),
			'operator' => array (
				'user' => array ('index', 'list', 'insert', 'update', 'update_status', 'delete', 'detail', 'm_form_user_info', 'insert_user_info', 'm_user_info'),
			),				
		);


