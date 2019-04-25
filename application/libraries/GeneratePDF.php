
<?php

/**
 * Class for Generate PDF and Excel
 * Use generate_pdf for generate PDF
 * Use generate_xls for generate XLS
 * Don't Edit Except By Me
 */

include_once("mpdf60/Mpdf.php");

class Generatepdf {
	// Private Variable
	private $mpdf;
	private $_content;
	private $_html;
	private $_text_top_1;
	private $_text_top_2;
	private $_text_bottom_1;
	private $_text_bottom_2;
	private $_disable_filter;
	private $_disable_header;
	private $_font_size;

	/**
	 * Constructor
	 */
	 //$content = content dari controller
	 //$tt1 = text top 1 (string)
	 //$tt2 = text top 2 (string)
	 //$tb1 = text bottom 1 (string)
	 //$tb2 = text bottom 2 (string)
	 //$df = disable text filter di cetakan (true or false)
	 //$dh = disable kop surat di cetakan (true or false or blank)
	 //$fs = ukuran font, (string) example '10'
	public function __construct($content=null){
		//error_reporting(E_ALL);
		//ini_set('display_errors', 'On');
		$this->_content = $content[0];
		//var_dump($this->_content);die;
		$this->_text_top_1 = $content[1];
		$this->_text_top_2 = $content[2];
		$this->_text_bottom_1 = $content[3];
		$this->_text_bottom_2 = $content[4];
		$this->_disable_filter = $content[5];
		$this->_disable_header = $content[6];
		//var_dump($this->_disable_header);die;
		if($fs === null){
			$this->_font_size = '12';
		}else{
			$this->_font_size = $content[7];
		}
		ob_clean();
	}

	/**
	 * Generate PDF
	 */
	public function generate_pdf($orientation=null, $paper=null, $costum=null, $watermark=null){
		$this->mpdf=new mPDF('c', $paper);

		if($costum ==  null){
			//odd
			$this->mpdf->SetHTMLHeader($this->set_pdf_header());
			//even
			$this->mpdf->SetHTMLHeader($this->set_pdf_header(), "E");
		}else if($costum == '2'){
			$this->mpdf->SetHTMLHeader($this->set_pdf_header2());
			$this->mpdf->SetHTMLHeader($this->set_pdf_header2(), "E");
		}else if($costum == '4'){
			$this->mpdf->SetHTMLHeader($this->set_pdf_header2());
			$this->mpdf->SetHTMLHeader($this->set_pdf_header2(), "E");
		}

		$this->mpdf->SetHTMLFooter($this->set_pdf_footer());
		if($this->_disable_header == true){
			$this->mpdf->SetMargins(15,15,15);
			if($this->_disable_header === 'blank'){
				$this->mpdf->SetMargins(15,15,35);
			}
		}else{
			if(Session::get('role') == 'pemda' or Session::get('role') == 'desa'){
				$this->mpdf->SetMargins(15,15,50);
			}else{
				$this->mpdf->SetMargins(15,15,35);
			}
		}

		$this->mpdf->AddPage($orientation);
		
		//watermark, fill parameter with url
		if($watermark !=null){
			$this->mpdf->SetWatermarkImage(
				$watermark[0], 
				$watermark[1], 
				$watermark[2], 
				$watermark[3]
			);
			$this->mpdf->showWatermarkImage = true;
		}
		
		if($costum ==  null){
			$this->set_html();
		}else if($costum == '1'){
			$this->set_html_costum1();
		}else if($costum == '2'){
			$this->set_html_costum2();
		}else if($costum == '3'){
			$this->set_html_costum3();
		}else{
			$this->set_html();
		}
		
		$this->mpdf->WriteHTML($this->_html);
		$this->mpdf->Output(str_replace('<br>',' ',$this->_content->data->title).'.pdf', 'D');
		exit;
	}

	/**
	 * Setter and Getter Method
	 */

	public function set_pdf_header(){
		//var_dump($this->_disable_header);
		if($this->_disable_header == true){
			$html = "";
		}else{
				$logo = URL."public/images/kemenkeu.png";
				$kemenkeu = "KEMENTERIAN KEUANGAN REPUBLIK INDONESIA";
				$djpb = "DIREKTORAT JENDERAL PERBENDAHARAAN";
				$user = Session::get('user');
				$html = <<<EOL
					<div>
						<table width="100%" align="center" style="font-family:Arial;letter-spacing:0px;font-size:12px" cellspacing="0" cellpadding="1" border="0">
							<tr>
								<td width="10%"><img height="50px" width="55px" src="$logo"></td>
								<td width="90%" style="font-size:12px;font-weight:bold">$kemenkeu<br>$djpb<br>$user</td>
							</tr>
							<tr>
							   <td colspan="2"><hr></td>
							</tr>
						</table>
					</div>
EOL;
		}

		return $html;
	}

	public function set_pdf_footer(){
		$html = '<div style="font-family:Arial;font-size:10px;text-align:center;padding-top: 5mm;">hal : {PAGENO} dari {nb} halaman</div>'.
				'<div style="font-family:Arial;font-size:9px;text-align:right">tanggal cetak : '.date("d-m-y H:i:s").' oleh : </div>';
		return $html;
	}

	public function set_html(){
		$fs = $this->_font_size;
		$this->_html  = '';
		$this->_html .= $this->set_title();
		$this->_html .= $this->set_text_top_1();
		$this->_html .= $this->set_text_top_2();

		if($this->_disable_filter == false){
			$this->_html .= '<div style="font-size:'.$fs.'px;">'.$this->set_filter().'</div><br>';
		}else{
			$this->_html .= '<br>';
		}
		

		if($this->_content != null){
			$this->_html .= '<table width="100%" style="font-family:Arial;padding-top:-10px;letter-spacing:0px;font-size:'.$fs.'px" cellspacing="0" cellpadding="0" border="0">';
			$this->_html .= $this->set_header();
			$this->_html .= $this->set_data();
            $this->_html .= $this->set_footer();
			$this->_html .= '</table>';
		}
		$this->_html .= $this->set_text_bottom_1();
		$this->_html .= $this->set_text_bottom_2();
	}

	public function set_html_costum1(){
		$fs = $this->_font_size;
		$this->_html  = '';
		$this->_html .= $this->set_title();
		$this->_html .= $this->set_text_top_1();
		$this->_html .= $this->set_text_top_2();
		if($this->_disable_filter == false){
			$this->_html .= '<div style="font-size:'.$fs.'px;">'.$this->set_filter().'</div><br>';
		}else{
			$this->_html .= '<br>';
		}

		if($this->_content != null){
			$i = 0;
			foreach($this->_content->data->content->fields as $value){
				if($i == 0){
					$this->_html .= "<table width='100%' style='margin:0;padding:0;font-size:".$fs."px;'>";
					$this->_html .= "<tr><td width='35%'>".$value->label."</td>";
					$this->_html .= "<td width='2%'>".":"."</td>";
					$this->_html .= "<td width='63%'>".$value->value."</td></tr>";
				}else if($i == 1){
					$this->_html .= "<tr><td width='35%'>".$value->label."</td>";
					$this->_html .= "<td width='2%'>".":"."</td>";
					$this->_html .= "<td width='63%'>".$value->value."</td></tr>";
				}else if($i == 2){
					$this->_html .= "<tr><td width='35%'>".$value->label."</td>";
					$this->_html .= "<td width='2%'>".":"."</td>";
					$this->_html .= "<td width='63%'>".$value->value."</td></tr>";

					$this->_html .= "<tr><td width='35%'>"."Tanggal"."</td>";
					$this->_html .= "<td width='2%'>".":"."</td>";
					$this->_html .= "<td width='63%'>".date('d')." ".strtoupper($this->convert_bulan(date('m')))." ".date('Y')."</td></tr>";

					$this->_html .= "</table><br>";
				}else if($i >= 4){
					$this->_html .= "<table width='100%' style='margin:0;padding:0;font-size:".$fs."px;' cellspacing='0' cellpadding='1' border='0'>";

					if($value->header != null){
						$myhead = $value->header;
						foreach($myhead as $he){
							$this->_html .= "<tr style='background-color:#DDDDDD'>";
							foreach($he as $h){
								$this->_html .= "<td align='center' style='border:solid 1px black;padding:5px 3px'>".$h->value."</td>";
							}
							$this->_html .= "</tr>";
						}
					}

					if($value->body != null){
						$mybody = $value->body;
						foreach($mybody as $bo){
							$this->_html .= "<tr>";
							foreach($bo as $b){
								$this->_html .= "<td style='border:solid 1px black'";
								if(isset($b->classes)){
									if(strpos(strtolower($b->classes), 'align-center') !== false){
										$this->_html .= ' align="center"';
									}else if(strpos(strtolower($b->classes), 'align-left') !== false){
										$this->_html .= ' align="left"';
									}else if(strpos(strtolower($b->classes), 'align-right') !== false){
										$this->_html .= ' align="right"';
									}
								}
								$this->_html .= '>';
								if(strpos(strtolower($b->classes), 'bold') !== false){
									$this->_html .= '<b>'.$b->value.'</b>';
								}else{
									$this->_html .= ''.$b->value;
								}
								$this->_html .= "</td>";
							}
							$this->_html .= "</tr>";
						}
					}
					$this->_html .= "</table><br>";
				}
				$i++;
			}
		}
		$this->_html .= $this->set_text_bottom_1();
		$this->_html .= $this->set_text_bottom_2();
	}

	public function set_html_costum2(){
		$fs = $this->_font_size;
		$this->_html  = '';
		$this->_html .= $this->set_title();
		$this->_html .= $this->set_text_top_1();
		$this->_html .= $this->set_text_top_2();
		$this->_html .= $this->set_text_bottom_1();
		$this->_html .= $this->set_text_bottom_2();
	}
	
	public function set_html_costum3(){
		$fs = $this->_font_size;
		$this->_html  = '';
		$this->_html .= $this->set_title();
		$this->_html .= $this->set_text_top_1();
		$this->_html .= $this->set_text_top_2();
		if($this->_content != null){
			$this->_html .= '<table width="100%" style="font-family:Arial;letter-spacing:0px;font-size:'.$fs.'px" cellspacing="0" cellpadding="1" border="0">';
			$this->_html .= $this->set_data();
			$this->_html .= '</table>';
		}		
		$this->_html .= $this->set_text_bottom_1();
		$this->_html .= $this->set_text_bottom_2();		
	}
	

	public function set_title(){
		if($this->_content->data->title != null){
			$title = $this->_content->data->title;
			$html =<<<EOL
						<div style="font-family:Arial;font-size:14px;font-weight:bold;text-align:center;text-transform:uppercase;padding-top:-20px">$title</div><br>
EOL;
		}else{
			$html = null;
		}
		return $html;
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
				}else if($filters->type=="daterange"){
					if($filters->startvalue!=NULL AND $filters->startvalue!='NULL'){
						array_push($array_url, 'Tanggal : '.$filters->startvalue);
					}
					if($filters->endvalue!=NULL AND $filters->endvalue!='NULL'){
						array_push($array_url, 's.d. &nbsp; '.$filters->endvalue);
					}
				}else if($filters->type=="selectgroup"){
					if($filters->leftvalue!=NULL AND $filters->leftvalue!='NULL' AND $filters->leftvalue!=""){
						array_push($array_url, 'Bulan : '.$filters->leftvalue);
					}
					if($filters->rightvalue!=NULL AND $filters->rightvalue!='NULL' AND $filters->rightvalue!=""){
						array_push($array_url, 's.d. '.$filters->rightvalue);
					}
				}else if($filters->type=="text"){
					if($filters->value!=NULL AND $filters->value!='NULL'){
						array_push($array_url, $filters->label.' : '.str_replace('-','/',$filters->value));
					}
				}else if($filters->type=="date"){
					if($filters->value!=NULL AND $filters->value!='NULL'){
						array_push($array_url, $filters->label.' : '.$filters->value);
					}
				}else if($filters->type=="hidden" AND $filters->value!='NULL'){
					if($filters->value!=NULL){
						array_push($array_url, $filters->label.' : '.$filters->value);
					}
				}
			}
			foreach($array_url as $value){
				$filter_menu .= $value;
				$filter_menu .= ' &nbsp; ';
				$filter_menu .= '  &nbsp; ';
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

	public function set_header(){
		if(isset($this->_content->data->header) != null){
			$html = '';
			foreach($this->_content->data->header as $head){
				$html .= '<tr style="background-color:#E0EBFF">';
				foreach($head as $value){
					if($this->remove_value($value->value) == false){
						$html .= '<th style="border:solid 1px #444444;padding:8px 4px"';
							if(isset($value->rowspan)){
								$html .= ' rowspan="'.$value->rowspan.'"';
							}
							if(isset($value->colspan)){
								$html .= ' colspan="'.$value->colspan.'"';
							}
							if(isset($value->classes)){
								if(strpos(strtolower($value->classes), 'align-center') !== false){
									$html .= ' align="center"';
								}else if(strpos(strtolower($value->classes), 'align-left') !== false){
									$html .= ' align="left"';
								}else if(strpos(strtolower($value->classes), 'align-right') !== false){
									$html .= ' align="right"';
								}
							}
							if(isset($value->width)){
								$html .= ' width="'.$value->width.'"';
							}
							$html .= '>';
							if(strpos(strtolower($value->classes), 'bold') !== false){
								$html .= '<b>'.$value->value.'</b>';
							}else{
								$html .= ''.$value->value;
							}
						$html .= '</th>';
					}
				}
				$html .= '</tr>';
			}
		}else{
			$html = null;
		}
		return $html;
	}

	public function set_data(){
		if(isset($this->_content->data->body) != null){
			$html = '';
			foreach($this->_content->data->body as $data){
				$html .= '<tr>';
				foreach($data as $value){
					$value->value = $this->cleaning_data($value->value);
					if($this->remove_value($value->value) == false){
						if(strpos(strtolower($value->classes), 'style-noborder') !== false){
							$html .= '<td style="padding:12px 4px"';
						}else if(strpos(strtolower($value->classes), 'style-pagebreak') !== false){
							$html .= '<td style="padding:16px 4px"';
						}else if(strpos(strtolower($value->classes), 'style-header') !== false){
							$html .= '<td style="border:solid 1px #444444;padding:8px 4px;background-color:#E0EBFF"';
						}else{
							$html .= '<td style="border:solid 1px #444444;padding:4px"';
						}
						if(isset($value->rowspan)){
							$html .= ' rowspan="'.$value->rowspan.'"';
						}
						if(isset($value->colspan)){
							$html .= ' colspan="'.$value->colspan.'"';
						}
						if(isset($value->classes)){
							if(strpos(strtolower($value->classes), 'align-center') !== false){
								$html .= ' align="center"';
							}else if(strpos(strtolower($value->classes), 'align-left') !== false){
								$html .= ' align="left"';
							}else if(strpos(strtolower($value->classes), 'align-right') !== false){
								$html .= ' align="right"';
							}
						}
						$html .= '>';
						if(strpos(strtolower($value->classes), 'bold') !== false){
							$html .= '<b>'.$value->value.'</b>';
						}else{
							$html .= ''.$value->value;
						}
						$html .= '</td>';
					}
				}
				$html .= '</tr>';
			}
		}else{
			$html = null;
		}
		return $html;
	}
	
	public function set_costum_data1(){
		if(isset($this->_content->data->body) != null){
			$html = '';
			foreach($this->_content->data->body as $data){
				$html .= '<tr>';
				foreach($data as $value){
					$value->value = $this->cleaning_data($value->value);
					if($this->remove_value($value->value) == false){
						if(strpos(strtolower($value->classes), 'style-noborder') !== false){
							$html .= '<td style="padding:15px 5px"';
						}else if(strpos(strtolower($value->classes), 'style-header') !== false){
							$html .= '<td style="border:solid 1px #444444;padding:10px 5px;background-color:#EEEEEE"';
						}else{
							$html .= '<td style="border:solid 1px #444444;padding:5px"';
						}
							if(isset($value->rowspan)){
								$html .= ' rowspan="'.$value->rowspan.'"';
							}
							if(isset($value->colspan)){
								$html .= ' colspan="'.$value->colspan.'"';
							}
							if(isset($value->classes)){
								if(strpos(strtolower($value->classes), 'align-center') !== false){
									$html .= ' align="center"';
								}else if(strpos(strtolower($value->classes), 'align-left') !== false){
									$html .= ' align="left"';
								}else if(strpos(strtolower($value->classes), 'align-right') !== false){
									$html .= ' align="right"';
								}
							}
							$html .= '>';
							if(strpos(strtolower($value->classes), 'bold') !== false){
								$html .= '<b>'.$value->value.'</b>';
							}else{
								$html .= ''.$value->value;
							}
						$html .= '</td>';
					}
				}
				$html .= '</tr>';
			}
		}else{
			$html = null;
		}
		return $html;
	}	

	public function set_footer(){
		if(isset($this->_content->data->footer) != null){
			$html = '';
			foreach($this->_content->data->footer as $data){
				$html .= '<tr>';
				foreach($data as $value){
					$html .= '<td style="border:solid 1px black"';
						if(isset($value->rowspan)){
							$html .= ' rowspan="'.$value->rowspan.'"';
						}
						if(isset($value->colspan)){
							$html .= ' colspan="'.$value->colspan.'"';
						}
						if(isset($value->classes)){
							if(strpos(strtolower($value->classes), 'align-center') !== false){
								$html .= ' align="center"';
							}else if(strpos(strtolower($value->classes), 'align-left') !== false){
								$html .= ' align="left"';
							}else if(strpos(strtolower($value->classes), 'align-right') !== false){
								$html .= ' align="right"';
							}
						}
						$html .= '>';
						if(strpos(strtolower($value->classes), 'bold') !== false){
							$html .= '<b>'.$value->value.'</b>';
						}else{
							$html .= ''.$value->value;
						}
					$html .= '</td>';
				}
				$html .= '</tr>';
			}
		}else{
			$html = null;
		}
		return $html;
	}
	
	public function set_table1(){
		if(isset($this->_content->data->table1) != null){
			$html = '';
			foreach($this->_content->data->table1 as $head){
				$html .= '<tr style="background-color:#FEFEFE">';
				foreach($head as $value){
					if($this->remove_value($value->value) == false){
						$html .= '<th style="border:solid 1px #444444;padding:5px"';
							if(isset($value->rowspan)){
								$html .= ' rowspan="'.$value->rowspan.'"';
							}
							if(isset($value->colspan)){
								$html .= ' colspan="'.$value->colspan.'"';
							}
							if(isset($value->classes)){
								if(strpos(strtolower($value->classes), 'align-center') !== false){
									$html .= ' align="center"';
								}else if(strpos(strtolower($value->classes), 'align-left') !== false){
									$html .= ' align="left"';
								}else if(strpos(strtolower($value->classes), 'align-right') !== false){
									$html .= ' align="right"';
								}
							}
							if(isset($value->width)){
								$html .= ' width="'.$value->width.'"';
							}
							$html .= '>';
							if(strpos(strtolower($value->classes), 'bold') !== false){
								$html .= '<b>'.$value->value.'</b>';
							}else{
								$html .= ''.$value->value;
							}
						$html .= '</th>';
					}
				}
				$html .= '</tr>';
			}
		}else{
			$html = null;
		}
		return $html;
	}	

	public function set_text_top_1(){
		$fs = $this->_font_size.'px';
		if($this->_text_top_1 != null){
			$text = $this->_text_top_1;
			$html =<<<EOL
						<span style="font-family:Arial;margin:0;padding:0;font-size:$fs;">$text</span>
EOL;
		}else{
			$html = null;
		}
		return $html;
	}
	public function set_text_top_2(){
		$fs = $this->_font_size.'px';
		if($this->_text_top_2 != null){
			$text = $this->_text_top_2;
			$html =<<<EOL
						<span style="font-family:Arial;margin:0;padding:0;font-size:$fs;">$text</span>
EOL;
		}else{
			$html = null;
		}
		return $html;
	}
	public function set_text_bottom_1(){
		$fs = $this->_font_size.'px';
		if($this->_text_bottom_1 != null){
			$text = $this->_text_bottom_1;
			$html =<<<EOL
						<span style="font-family:Arial;margin:0;padding:0;font-size:$fs;">$text</span>
EOL;
		}else{
			$html = null;
		}
		return $html;
	}
	public function set_text_bottom_2(){
		$fs = $this->_font_size.'px';
		if($this->_text_bottom_2 != null){
			$text = $this->_text_bottom_2;
			$html =<<<EOL
						<span style="font-family:Arial;margin:0;padding:0;font-size:$fs;">$text</span>
EOL;
		}else{
			$html = null;
		}
		return $html;
	}

	public function remove_value($value){
		$status = false;
		$remove_value = array('ubah', 'hapus', 'aksi', 'cetak', '---', 'proses/detail', 'proses', 'detail', 'tidak bisa diubah', 'tidak bisa dihapus','ubah nilai kebutuhan');
		foreach($remove_value as $val){
			if(strtolower($value) == $val){
				$status = true;
			}
		}
		return $status;
	}

	public function cleaning_data($value){
		$return_value = $value;
		if($this->is_link($value) !== false){
			$return_value = $this->remove_link($return_value);
		}
		return $return_value;
	}

	public function is_link($value){
		$find = "<a ";
		return strpos($value, $find);
	}

	public function remove_link($value){
		$find1 = ">";
		$find2 = "</a>";
		$position1 = strpos($value, $find1);
		$position2 = strpos($value, $find2);
		return substr($value, $position1+1, $position2-$position1-1);
	}

	private function convert_bulan($bulan){
		if($bulan == '01'){
			return 'Januari';
		}else if ($bulan == '02'){
			return 'Februari';
		}else if($bulan == '03'){
			return 'Maret';
		}else if($bulan == '04'){
			return 'April';
		}else if($bulan == '05'){
			return 'Mei';
		}else if($bulan == '06'){
			return 'Juni';
		}else if($bulan == '07'){
			return 'Juli';
		}else if($bulan == '08'){
			return 'Agustus';
		}else if($bulan == '09'){
			return 'September';
		}else if($bulan == '10'){
			return 'Oktober';
		}else if($bulan == '11'){
			return 'November';
		}else if($bulan == '12'){
			return 'Desember';
		}else{
			return '';
		}
	}

	public function __destruct(){

	}

}
