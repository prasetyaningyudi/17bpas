<?php

/**
 * Class for Generate PDF and Excel
 * Use generate_pdf for generate PDF
 * Use generate_xls for generate XLS
 * Don't Edit Except By Me
 */
 
//include_once("TCPDF-master/tcpdf.php");
include_once("PHPExcel.php");
 
class Generatexls {
	// Private Variable
	private $_objPHPExcel;
	private $_cursor_a;
	private $_cursor_b;
	private $_content;
	
	/**
	 * Constructor
	 */	
	public function __construct($content){
		error_reporting(E_ALL);
		ini_set('display_errors', 'On');
		$this->_content = $content[0];
		$this->_cursor_a = 'A';
		$this->_cursor_b = '1';
		ob_clean();		
	}
	
	/**
	 * Generate PDF
	 */		
	public function generate_xls(){
		// create new XLS document
		$this->_objPHPExcel = new PHPExcel();
		$title = $this->set_title();
		$titleXLS = $title.'.xlsx';
		$this->_objPHPExcel->getProperties()->setCreator("Yudi Prasetyo")
									 ->setLastModifiedBy("Yudi Prasetyo")
									 ->setTitle($title)
									 ->setSubject($title)
									 ->setDescription($title)
									 ->setKeywords("utakata xls")
									 ->setCategory("utakata xls");		

		//$this->set_html();

		// This method has several options
		$this->_objPHPExcel->setActiveSheetIndex(0);
		$this->_objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$this->_objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL);
		
		$this->placing_title();
		$this->placing_filter();
		$this->placing_header();
		$this->placing_data_new();
		
		$this->_objPHPExcel->getActiveSheet()
			->getStyle('A1:'.$this->_cursor_a.$this->_cursor_b)
			->getNumberFormat()
			->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );		
		
		//Generate Output
		$objWriter = PHPExcel_IOFactory::createWriter($this->_objPHPExcel, "Excel2007");
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$titleXLS\"");
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($this->_objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;		
	}
	
	/**
	 * Setter and Getter Method
	 */	

	public function set_title(){
		if($this->_content->data->title !== null){
			$text = $this->replace_break_space($this->_content->data->title);
			$text = strtoupper($text);
		}else{
			$text = '';
		}
		return $text;
	}
	
	public function replace_break_space($value){
		$find = "<br>";
		$find2 = "</br>";
		if(strpos(strtolower($value), $find) !== false){
			return str_replace("<br>", " ", $value);
		}if(strpos(strtolower($value), $find2) !== false){
			return str_replace("<br>", " ", $value);
		}else{
			return $value;
		}		
	}
	
	public function placing_title(){
		if($this->_content->data->title !== null){
			$count_br = 0;
			$count_br = substr_count($this->_content->data->title, '<br>');
			if($count_br > 0){
				$title = explode("<br>", $this->_content->data->title);
				foreach($title as $value){
					$this->_objPHPExcel->getActiveSheet()->setCellValue($this->_cursor_a.$this->_cursor_b++, strtoupper($value));
				}
			}else{
				$this->_objPHPExcel->getActiveSheet()->setCellValue($this->_cursor_a.$this->_cursor_b, strtoupper($this->_content->data->title));
			}	
		}else{
			$this->_objPHPExcel->getActiveSheet()->setCellValue($this->_cursor_a.$this->_cursor_b, "Cetakan XLS");
		}
		$this->_cursor_b++;
	}
	
	public function placing_filter(){
		$this->_objPHPExcel->getActiveSheet()->setCellValue($this->_cursor_a.$this->_cursor_b++, $this->set_filter());
	}
	
	public function placing_header(){
		if(isset($this->_content->data->header) != null){
			foreach($this->_content->data->header as $head){
				$current_cursor_a = $this->_cursor_a;
				foreach($head as $value){
					$this->is_merger();
					if(isset($value->colspan)){
						$to_cell = $this->_cursor_a;
						for($i=1;$i<$value->colspan;$i++){
							$to_cell++;
						}
						$this->_objPHPExcel->getActiveSheet()->mergeCells($this->_cursor_a.$this->_cursor_b.':'.$to_cell.$this->_cursor_b);
						$this->centering_text($this->_cursor_a.$this->_cursor_b.':'.$to_cell.$this->_cursor_b);
						$posisi_multicol[]=$to_cell.$this->_cursor_b;
						if(isset($value->rowspan)){
							$to_cell_r = $this->_cursor_b;
							for($i=1;$i<$value->rowspan;$i++){
								$to_cell_r++;
							}
							$this->_objPHPExcel->getActiveSheet()->mergeCells($this->_cursor_a.$this->_cursor_b.':'.$this->_cursor_a.$to_cell_r);
							$this->centering_text($this->_cursor_a.$this->_cursor_b.':'.$this->_cursor_a.$to_cell_r);
						}
						$this->_objPHPExcel->getActiveSheet()->setCellValue($this->_cursor_a++.$this->_cursor_b, $this->remove_value($this->cleaning_data($value->value)));
						$this->_cursor_a = $to_cell;
						$this->_cursor_a++;						
					}else{
						if(isset($value->rowspan)){
							$to_cell = $this->_cursor_b;
							for($i=1;$i<$value->rowspan;$i++){
								$to_cell++;
							}
							$this->_objPHPExcel->getActiveSheet()->mergeCells($this->_cursor_a.$this->_cursor_b.':'.$this->_cursor_a.$to_cell);
							$this->centering_text($this->_cursor_a.$this->_cursor_b.':'.$this->_cursor_a.$to_cell);
						}	
						$this->centering_text($this->_cursor_a.$this->_cursor_b);
						$this->_objPHPExcel->getActiveSheet()->setCellValue($this->_cursor_a++.$this->_cursor_b, $this->remove_value($this->cleaning_data($value->value)));
					}
				}
				$this->_cursor_a = $current_cursor_a;
				$this->_cursor_b++;
			}			
		}else{
			//do nothing
		}		
	}
	
	public function placing_data_new(){
		if(isset($this->_content->data->body) != null){
			foreach($this->_content->data->body as $data){
				$current_cursor_a = $this->_cursor_a;
				foreach($data as $value){
					if($this->is_hidden($value->classes) == true){
						
					}else{
						$this->is_merger();
						if(isset($value->colspan)){
							$to_cell = $this->_cursor_a;
							for($i=1;$i<$value->colspan;$i++){
								$to_cell++;
							}
							$this->_objPHPExcel->getActiveSheet()->mergeCells($this->_cursor_a.$this->_cursor_b.':'.$to_cell.$this->_cursor_b);
							//$this->centering_text($this->_cursor_a.$this->_cursor_b.':'.$to_cell.$this->_cursor_b);
							$posisi_multicol[]=$to_cell.$this->_cursor_b;
							if(isset($value->rowspan)){
								$to_cell_r = $this->_cursor_b;
								for($i=1;$i<$value->rowspan;$i++){
									$to_cell_r++;
								}
								$this->_objPHPExcel->getActiveSheet()->mergeCells($this->_cursor_a.$this->_cursor_b.':'.$this->_cursor_a.$to_cell_r);
								//$this->centering_text($this->_cursor_a.$this->_cursor_b.':'.$this->_cursor_a.$to_cell_r);
							}
							$this->_objPHPExcel->getActiveSheet()->setCellValue($this->_cursor_a++.$this->_cursor_b, $this->remove_value( $this->cleaning_data($value->value)));
							$this->_cursor_a = $to_cell;
							$this->_cursor_a++;						
						}else{
							if(isset($value->rowspan)){
								$to_cell = $this->_cursor_b;
								for($i=1;$i<$value->rowspan;$i++){
									$to_cell++;
								}
								$this->_objPHPExcel->getActiveSheet()->mergeCells($this->_cursor_a.$this->_cursor_b.':'.$this->_cursor_a.$to_cell);
								//$this->centering_text($this->_cursor_a.$this->_cursor_b.':'.$this->_cursor_a.$to_cell);
							}	
							$this->centering_text($this->_cursor_a.$this->_cursor_b);
							$this->_objPHPExcel->getActiveSheet()->setCellValue($this->_cursor_a++.$this->_cursor_b, $this->remove_value( $this->cleaning_data($value->value)));
						}
					}
				}
				$this->_cursor_a = $current_cursor_a;
				$this->_cursor_b++;
			}			
		}else{
			//do nothing
		}			
	}

	public function placing_data(){
		if(isset($this->_content->data->body) != null){
			foreach($this->_content->data->body as $data){
				$current_cursor_a = $this->_cursor_a;
				foreach($data as $value){
					$this->_objPHPExcel->getActiveSheet()->setCellValue($this->_cursor_a++.$this->_cursor_b, $this->cleaning_data($value->value));
				}	
				$this->_cursor_a = $current_cursor_a;
				$this->_cursor_b++;
			}			
		}else{
			//donothing
		}	
	}
	
	
	
	public function cleaning_data($value){
		$return_value = $value;
		if($this->is_link($value) !== false){
			$return_value = $this->remove_link($return_value);
		}
		if($this->is_number($return_value) !== false){
			$return_value = $this->keep_zero($this->remove_koma($return_value));
		}
		if($this->is_break($return_value) !== false){
			$return_value = str_replace("<br>", " ", $return_value);
		}
        $return_value =  $this->remove_html_tag($return_value);
        $return_value =  $this->remove_value($return_value);        
		return $return_value;
	}
	
	public function is_hidden($value){
		if(strpos($value, 'hidden') !== false){
			return true;
		}else{
			return false;
		}
	}

    public function remove_html_tag($value){
        $html_tag = array("<b>","</b>","<i>","</i>","<u>","</u>","<p>","</p>","&nbsp;");
        foreach($html_tag as $row){
            if(strpos($value, $row) !== false){
                $value = str_replace($row, "", $value);
                
            }
        }
        return $value;
        
    }
    
    public function remove_value($value){
        $string_tag = array("<a onclick", "detail", "ubah", "hapus", "tidak bisa diubah", "tidak bisa dihapus", "---", "action");
        foreach($string_tag as $row){
            if(strpos(strtolower($value), $row) !== false){
                $value = "";
            }
        }
        return $value;        
    }
			
	
	public function is_merger(){
		$cell = $this->_objPHPExcel->getActiveSheet()->getCell($this->_cursor_a.$this->_cursor_b);
		if ($cell->isInMergeRange() === true) {
				$this->_cursor_a++;
				$this->is_merger();						
		}	
	}
	
	public function centering_text($value){
		$this->_objPHPExcel->getActiveSheet()->getStyle($value)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->_objPHPExcel->getActiveSheet()->getStyle($value)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	
	}

	public function set_filter(){
		$filter_menu='';		
		if(isset($this->_content->data->filters) != null){
			$array_url = array();
			foreach($this->_content->data->filters as $filters){
				if($filters->type=="select"){
					if($filters->value!=NULL AND $filters->value!='NULL'){
						if(isset($filters->options) != null){
							array_push($array_url, $filters->label.' : '.$this->get_label($filters->options,$filters->value));
						}else{
							array_push($array_url, $filters->label.' : '.$filters->value);
						}
					}
				}else if($filters->type=="date"){
					
				}else if($filters->type=="daterange"){
					if($filters->value!=NULL AND $filters->value!='NULL'){
						array_push($array_url, $filters->label.' : '.str_replace('-','/',$filters->value));
					}					
				}else if($filters->type=="text"){
					if($filters->value!=NULL AND $filters->value!='NULL'){
						array_push($array_url, $filters->label.' : '.str_replace('-','/',$filters->value));
					}				
				}else if($filters->type=="hidden" AND $filters->value!='NULL'){
					if($filters->value!=NULL){
						array_push($array_url, $filters->label.' : '.$filters->value);
					}				
				}	
			}
			foreach($array_url as $value){
				$filter_menu .= $value;
				$filter_menu .= ' ';
				$filter_menu .= ' ';
			}
		}else{
			$filter_menu = null;
		}
		return $filter_menu;
	}
	
	public function get_label($options, $find){
		$label='';
		foreach($options as $value){
			if($find == $value->value){
				$label = $value->label;
			}
		}
		return $label;
	}
	
	public function is_link($value){
		$find = "<a href";
		return strpos($value, $find);
	}	

	public function is_break($value){
		$find = "<br>";
		return strpos($value, $find);		
	}
	
	public function is_number($value){
		if(is_numeric (substr($value, 0, 1))){
			return true;
		}else if(substr($value, 0, 1) == '-'){
			return true;
		}else{
			return false;
		}
	}
	
	public function remove_link($value){
		$find1 = ">";
		$find2 = "</a>";
		$position1 = strpos($value, $find1);
		$position2 = strpos($value, $find2);
		return substr($value, $position1+1, $position2-$position1-1); 
	}
		
	public function keep_zero($value){
		if($value == '0'){
			return $value;
		}else if(strpos(substr($value, 0, 2), '0.') !== false){
			return $value;
		}else if(strpos(substr($value, 0, 1), '0') !== false){
			return str_replace($value, $value, $value);
		}else{
			return $value;
		}
	}
	public function remove_koma($value){
		return str_replace(',', '', $value);
	}			
	
	public function __destruct(){
		
	}
	
}
