var the_data;
var targeturl;
var fromfilter;
var frompaging;
var current_offset;
var globallimit;
var globalform;
var field_data;

function openWindow(url){
	targeturl = url;
	var listurl='';
	console.log(targeturl);
	var array_split = targeturl.split("/");
	array_split.forEach(function(item) {
		if(item == ''){
			
		}else{
			listurl = listurl+item+'/';
		}
	});
	$.ajax({
		type  : 'ajax',
		url   : listurl,
		async : true,
		dataType : 'json',
		success : function(data){
			$('#show-data').html('');
			from_filter(false);
			get_data(data);
		}
	});		
}

function initiation(url){
	targeturl = url;
	$.ajax({
		type  : 'ajax',
		url   : targeturl + '/lists',
		async : true,
		dataType : 'json',
		success : function(data){
			from_filter(false);
			get_data(data);
		}
	});	
}

function initiation_target(url){
	targeturl = url;
	$.ajax({
		type  : 'ajax',
		url   : targeturl,
		async : true,
		dataType : 'json',
		success : function(data){
			from_filter(false);
			get_data(data);
		}
	});	
}

function from_filter(ff){
	fromfilter = ff;	
}

//input record
$('.button-add').on('click',function(){
	$.ajax({
		type : "POST",
		url  : targeturl+'/insert',
		dataType : "JSON",
		success: function(data){
			get_data(data);
		}
	});
	return true;
});

$('#button-save').on('click',function(e){
	//e.preventDefault();
	var datainput = generate_form_data("#form-add");
	console.log(datainput);
	
	$.ajax({
		type : "POST",
		url  : targeturl+'/insert',
		data: datainput,
		cache: false,
		contentType: false,
		processData: false,
		success: function(data){
			$('#modal-add').modal('hide');
			//console.log(data);
			from_filter(false);
			get_data(JSON.parse(data));
			$("#form-add").trigger("reset");
			initiation(targeturl);			
		}
	});

	return false;
});


//get data for update record
$('#show-data').on('click','.item-edit',function(){
	var id = $(this).attr("id");
	$.ajax({
		type : "POST",
		url  : targeturl+'/update',
		dataType : "JSON",
		data : {id:id},
		success: function(data){
			get_data(data);
		}
	});
	return true;
});

//update data
$('#button-update').on('click',function(e){
	//e.preventDefault();
	var datainput = generate_form_data("#form-edit");
	//console.log(datainput);
	$.ajax({
		type : "POST",
		url  : targeturl+'/update',
		data: datainput,
		cache: false,
		contentType: false,
		processData: false,
		success: function(data){
			$('#modal-edit').modal('hide');
			from_filter(false);
			get_data(JSON.parse(data));
			initiation(targeturl);			
		}
	});

	return false;
});

//saving record
$('#button-submit-filter').on('click',function(){
	var datainput = generate_json_from_field("#form-filter");
	//console.log(datainput);
	$.ajax({
		type : "POST",
		url  : targeturl+'/lists',
		dataType : "JSON",
		data : JSON.parse(datainput),
		success: function(data){
			from_filter(true);
			get_data(data);
			$('body').toggleClass('full-width semi-width');
		}
	});

	return false;
});

$('#button-submit-filter-first').on('click',function(){
	var datainput = generate_json_from_field("#form-filter-first");
	//console.log(datainput);
	$.ajax({
		type : "POST",
		url  : targeturl+'/lists',
		dataType : "JSON",
		data : JSON.parse(datainput),
		success: function(data){
			from_filter(true);
			get_data(data);
			$('ul#myTab-filter-first li:eq(0)').removeClass('active');
			$('ul#myTab-filter-first li:eq(1)').addClass('active');
			$('#tab_filter1').removeClass('active');
			$('#tab_filter1').removeClass('in');
			$('#tab_filter2').addClass('active');
			$('#tab_filter2').addClass('in');			
		}
	});

	return false;
});

//get data for delete record
$('#show-data').on('click','.item-delete',function(){
	var id = $(this).attr("id");
	$('#modal-delete').modal('show');
	$('[name="id"]').val(id);
});

//delete record to database
 $('#button-delete').on('click',function(){
	var id = $('#id').val();
	$.ajax({
		type : "POST",
		url  : targeturl+'/delete',
		dataType : "JSON",
		data : {id:id},
		success: function(data){
			$('#modal-delete').modal('hide');
			from_filter(false);
			get_data(data);
			initiation(targeturl);
		}
	});
	return true;
});

//get data for update status record
$('#show-data').on('click','.item-status',function(){
	var id = $(this).attr("id");
	$.ajax({
		type : "POST",
		url  : targeturl+'/update_status',
		dataType : "JSON",
		data : {id:id},
		success: function(data){
			from_filter(false);
			get_data(data);
			initiation(targeturl);
		}
	});
	return true;
});

//get data for detail record
$('#show-data').on('click','.item-detail',function(){
	var id = $(this).attr("id");
	$.ajax({
		type : "POST",
		url  : targeturl+'/detail',
		dataType : "JSON",
		data : {id:id},
		success: function(data){
			get_data(data);
		}
	});
	return true;
});

function set_filter_title(){
	if(fromfilter == true){
		var html = '';
		var j = 0
		var myArray = [];
		for(i=0;i<the_data.data.filters.length;i++){
			if(the_data.data.filters[i].value != '' && the_data.data.filters[i].value != null){
				if(the_data.data.filters[i].type == 'select'){
					for(var k = 0; k<the_data.data.filters[i].options.length; k++){
						//console.log(the_data.data.filters[i].options[k].value);
						if(the_data.data.filters[i].options[k].value == the_data.data.filters[i].value){
							myArray.push('<span class="text-body">'+the_data.data.filters[i].label+' : </span><span class=".text-secondary">'+the_data.data.filters[i].options[k].label+'</span>');
						}
					}
				}else{
					myArray.push('<span class="text-body">'+the_data.data.filters[i].label+' : </span><span class=".text-secondary">'+the_data.data.filters[i].value+'</span>');
				}
				j++;
			}
		}
		if(j > 1){
			html +=	'<span class="text-dark">'+j+' Filters <i class="fa fa-caret-right"></i></span> ';
		}else{
			html +=	'<span class="text-dark">A Filter <i class="fa fa-caret-right"></i></span> ';
		}
		html += ''+myArray.join(', ');
		$('.title-information').html(html);
	}else{
		var html = '';
		$('.title-information').html(html);
	}
}

function set_pagination(){
	if(the_data.data.pagination != null){
		var limit = the_data.data.pagination[0];
		globallimit = limit;
		var offset = the_data.data.pagination[1];
		var total = the_data.data.pagination[2];
		var iterasi = Math.ceil(total/limit);
		//console.log('total: ' + total);
		//console.log('Iterasi: '+ iterasi);
		//console.log(total/limit);
		//console.log('Perpage: '+ limit);
		
		html = '<nav aria-label="pagination-example">';
		html +=	'<ul class="pagination justify-content-center">';
		
		if(offset == '0'){
			html +=	'<li class="page-item disabled"><a href="javascript:void(0)" numb="" class="page-link">Prev</a></li>';
		}else{
			html +=	'<li class="page-item"><a href="javascript:void(0)" numb="'+(offset/limit)+'" class="page-link">Prev</a></li>';
		}					
		
		for(var i = 0; i<iterasi; i++){
			if(offset/limit == i){
				html +=	'<li class="page-item active"><a href="javascript:void(0)" numb="'+(i+1)+'" class="page-link">'+(i+1)+'</a></li>';
			}else{
				html +=	'<li class="page-item"><a href="javascript:void(0)" numb="'+(i+1)+'" class="page-link">'+(i+1)+'</a></li>';
			}
		}
		if((total - offset) <= limit){
			html +=	'<li class="page-item disabled"><a class="page-link">Next</a></li>';
		}else{
			html +=	'<li class="page-item"><a href="javascript:void(0)" numb ="'+(offset/limit+2)+'" class="page-link">Next</a></li>';
		}	
		
		html +=	'</ul>';
		html +=	'</nav>';
		$('.paging').html(html);
	}
}

function set_pagination_dropdown(){
	if(the_data.data.pagination != null){
		var limit = the_data.data.pagination[0];
		globallimit = limit;
		var offset = the_data.data.pagination[1];
		var total = the_data.data.pagination[2];
		var iterasi = Math.ceil(total/limit);
		//console.log('total: ' + total);
		//console.log('Iterasi: '+ iterasi);
		//console.log(total/limit);
		//console.log('Perpage: '+ limit);
		
		html = '<nav aria-label="pagination-example">';
		html +=	'<ul class="pagination justify-content-center">';
		if(offset == '0'){
			html +=	'<li class="page-item disabled"><a href="javascript:void(0)" numb="" class="page-link">Prev</a></li>';
		}else{
			html +=	'<li class="page-item"><a href="javascript:void(0)" numb="'+(offset/limit)+'" class="page-link">Prev</a></li>';
		}					

		for(var i = 0; i<iterasi; i++){
			if(offset/limit == i){
				html +=	'<li class="page-item active"><a href="javascript:void(0)" numb="'+(i+1)+'" class="page-link">'+(i+1)+'</a></li>';
			}else{
				//html +=	'<li class="page-item"><a href="javascript:void(0)" numb="'+(i+1)+'" class="page-link">'+(i+1)+'</a></li>';
			}
		}

		if((total - offset) <= limit){
			html +=	'<li class="page-item disabled"><a class="page-link">Next</a></li>';
		}else{
			html +=	'<li class="page-item"><a href="javascript:void(0)" numb ="'+(offset/limit+2)+'" class="page-link">Next</a></li>';
		}	
		

		html += '<li>';
		html += '<div class="btn-group dropup">';
		html += '<button data-toggle="dropdown" class="btn btn-default" type="button" aria-expanded="false">';
		html += 'halaman <span class="caret"></span> </button><ul class="dropdown-menu">';
		for(var i = 0; i<iterasi; i++){
			if(offset/limit == i){
				html +=	'<li class="page-item active"><a href="javascript:void(0)" numb="'+(i+1)+'" class="page-link">'+(i+1)+'</a></li>';
			}else{
				html +=	'<li class="page-item"><a href="javascript:void(0)" numb="'+(i+1)+'" class="page-link">'+(i+1)+'</a></li>';
			}
		}		
		html += '</ul></div>';
		html += '</li>';
		
		html +=	'</ul>';
		html +=	'</nav>';
		
		$('.paging').html(html);
	}
}

$('.paging').on('click','a.page-link',function(){
	console.log(fromfilter);
	current_offset = ($(this).attr('numb') - 1) * globallimit;
	if(fromfilter == false){
		var datainput='{';
		datainput += '"offset":'+current_offset+',';
		datainput += '"submit":"sumbit"';
		datainput += '}';
		//console.log(datainput);
	}else{
		if(the_data.data.filterfirst == true){
			var datainput = generate_json_from_field("#form-filter-first");			
		}else{
			var datainput = generate_json_from_field("#form-filter");
		}
		datainput.length;
		datainput = datainput.slice(0,(datainput.length-1));
		datainput += ',"offset":"'+current_offset+'"}';
		//console.log(datainput);
	}
	frompaging = true;
	$.ajax({
		type : "POST",
		url  : targeturl+'/lists',
		dataType : "JSON",
		data : JSON.parse(datainput),
		success: function(data){
			get_data(data);
		}
	});
	return false;
});

function expected_output(type){
	if(type == 'xls'){
		console.log('xls generate');
	}else if(type == 'pdf'){
		console.log('pdf generate');
	}
	if(fromfilter == false){
		var datainput='';
		datainput += 'submit=submit';
	}else{
		if(the_data.data.filterfirst == true){
			var datainput = generate_param_from_field("#form-filter-first");
		}else{
			var datainput = generate_param_from_field("#form-filter");
		}
	}
	if(frompaging == true){
		datainput += '&offset='+current_offset;
	}
	
	if(type == 'xls'){
		datainput += '&expected_output=xls';
	}else if(type == 'pdf'){
		datainput += '&expected_output=pdf';
	}	
	
	
	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function(){
		if (this.readyState == 4 && this.status == 200) {

		}
	}
	xhr.open('POST', targeturl+'/lists', true);
	xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xhr.responseType = 'blob';
	xhr.onload = function() {
		// Only handle status code 200
		if(xhr.status === 200) {
			// Try to find out the filename from the content disposition `filename` value
			var filename = '';
			if (xhr.getResponseHeader('Content-Disposition').indexOf('"') > -1) {
				filename = xhr.getResponseHeader('Content-Disposition').match(/filename="(.+)"/)[1];
			} else {
				filename = xhr.getResponseHeader('Content-Disposition').match(/Filename=(.+)/)[1];
			}
			if (filename[filename.length-1] == '"') {
				filename = filename.substring(filename, filename.length - 1);
			}
			console.log(filename);
			
			// The actual download
			if(type == 'xls'){
				var blob = new Blob([xhr.response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
			}else if(type == 'pdf'){
				var blob = new Blob([xhr.response], { type: 'application/pdf' });
			}			
			
			var link = document.createElement('a');
			link.href = window.URL.createObjectURL(blob);
			link.download = filename;
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		}

	  // some error handling should be done here...
	};
	
	console.log(datainput);
	xhr.send(datainput); 			
	
	return false;
}

function get_data(data){
	console.log('from filter : '+fromfilter);
	the_data = data;
	
	if(data.type == 'table_default'){
		//init_display();		
		hide_toolbar();
		
		if(the_data.data.filterfirst == true){
			console.log('filter-first');
			$('.button-filter').hide();
			$('.tab-filter-first').show();
			$('#show-data-filter-first').html(generate_table(the_data.data));
			if(the_data.data.filters != null){
				$('.content-filter-first .filter-first-body').html(generate_form(true));
			}					
		}else{
			$('.main-table').show();
			$('#show-data').html(generate_table(the_data.data));
			if(the_data.data.filters != null){
				$('.modal-filter .filter-body').html(generate_form(true));
			}			
		}
		set_pagination_dropdown();		
		set_filter_title();
	}else if(data.type == 'table_two'){
		//init_display();		
		hide_toolbar();
		console.log('table_two');
		$('.tab-two-content').show();
		//console.log(the_data.data.table1);
		if(the_data.data.table1.subtitle != null){
			$('#content-tab1').html(the_data.data.table1.subtitle);
		}
		if(the_data.data.table2.subtitle != null){
			$('#content-tab2').html(the_data.data.table2.subtitle);
		}
		if(the_data.data.filters != null){
			$('.modal-filter .filter-body').html(generate_form(true));
		}			
		$('#show-data-content-one').html(generate_table(the_data.data.table1));	
		$('#show-data-content-two').html(generate_table(the_data.data.table2));	
		
		console.log('active : '+the_data.data.active);
		if(the_data.data.active != null || the_data.data.active != ''|| the_data.data.active != undefined){
			if(the_data.data.active == '1'){
				$('ul#myTab-two-content li:eq(1)').removeClass('active');
				$('ul#myTab-two-content li:eq(0)').addClass('active');
				$('#tab_content2').removeClass('active');
				$('#tab_content2').removeClass('in');
				$('#tab_content1').addClass('active');
				$('#tab_content1').addClass('in');					
			}else if(the_data.data.active == '2'){
				$('ul#myTab-two-content li:eq(0)').removeClass('active');
				$('ul#myTab-two-content li:eq(1)').addClass('active');
				$('#tab_content1').removeClass('active');
				$('#tab_content1').removeClass('in');
				$('#tab_content2').addClass('active');
				$('#tab_content2').addClass('in');				
			}
		}
		
	}else if(data.type == 'insert_default'){
		$('#modal-add .modal-body').html(generate_form(false));
		$('#modal-add').modal('show');
	}else if(data.type == 'update_default'){
		$('#modal-edit .modal-body').html(generate_form(false));
		$('#modal-edit').modal('show');	
	}else if(data.type == 'detail_default'){
		$('#modal-detail .modal-body').html(generate_table(the_data.data));
		$('#modal-detail').modal('show');		
	}else if(data.type == 'error'){
		var i;
		var info = '<ul>';
		for(i=0; i<the_data.data.info.length; i++){
			info += '<li>';
			info += the_data.data.info[i];
			info += '</li>';
		}
		info += '</ul>';
		$('#modal-info .modal-title').html('Information : ERROR');
		$('#modal-info .modal-body').html('<div class="alert alert-light" role="alert">'+info+'</div>');
		$('#modal-info').modal('show');
		set_filter_title();	
	}else if(data.type == 'success'){
		var i;
		var info = '<ul>';
		for(i=0; i<the_data.data.info.length; i++){
			info += '<li>';
			info += the_data.data.info[i];
			info += '</li>';
		}
		info += '</ul>';		
		$('#modal-info .modal-title').html('Information : Success');
		$('#modal-info .modal-body').html('<div class="alert alert-light" role="alert">'+info+'</div>');
		$('#modal-info').modal('show');
		set_filter_title();			
	}else if(data.type == 'modal_form'){	
		$('.for_modal').html(generate_modal());
		var html = '<input role="button" type="reset" id="button-reset" name="reset" class="btn btn-info" value="reset">';
		html += '<input role="button" type="submit" id="button-submit" name="submit" class="btn btn-primary" value="submit" style="margin-left: 30px;">';	
		$('#'+the_data.data.id+' .modal-footer').html(html);		
		$('#'+the_data.data.id+' .modal-body').html(generate_form(false));				
		$('#'+the_data.data.id).modal('show');
		onclik_submit();
	}else if(data.type == 'modal_table'){
		$('.for_modal').html(generate_modal());
		$('#'+the_data.data.id+' .modal-body').html(generate_table(the_data.data));
		var html = '<button type="button" class="mr-auto btn btn-secondary" data-dismiss="modal">Close</button>';
		$('#'+the_data.data.id+' .modal-footer').html(html);		
		$('#'+the_data.data.id).modal('show');
	}
	//console.log(field_data);
		if(field_data != undefined){
			set_daterange();
			set_date();
		}
		if(the_data.data.title != null || the_data.data.title != '' || the_data.data.title != undefined){
			$('.right_col h4').html(the_data.data.title);
		}
}

function set_daterange(){
	for(i=0;i<field_data.length;i++){
		if(field_data[i].type == 'daterange'){
			$('input[name="'+field_data[i].name+'"]').daterangepicker({
				locale: {
				  format: field_data[i].format,
				}
			});
		}
	}
	
}

function set_date(){
	for(i=0;i<field_data.length;i++){
		if(field_data[i].type == 'date'){
			$('input[name="'+field_data[i].name+'"]').datetimepicker({
				  format: field_data[i].format
			});
		}
	}
	
}

function onclik_submit(){
	$('.for_modal #button-submit').on('click',function(e){
		//e.preventDefault();
		var datainput = generate_form_data("#form-"+the_data.data.id);
		
		$.ajax({
			type : "POST",
			url  : the_data.data.target,
			data: datainput,
			cache: false,
			contentType: false,
			processData: false,
			success: function(data){
				var reload = false;
				if (typeof the_data.data.reload == "undefined" || the_data.data.reload == null){
					reload = false;
				}else{
					if(the_data.data.reload == true){
						reload = true;
					}
				}				
				$('#'+the_data.data.id).modal('hide');
				from_filter(false);
				get_data(JSON.parse(data));
				$("#form-"+the_data.data.id).trigger("reset");
				if(reload == true){
					initiation(targeturl);
				}
			}
		});

		return false;
	});
}

function generate_form_data(selector){
	console.log('generate from data from field');
	var field = $(selector).find( "[name]" );
	var formData = new FormData();

	$(field).each(function(index,element){
		if($(this).is(':checkbox')){
			if($(this).is( ':checked' )){
				formData.append(element.name, 'on');
			}else{
				formData.append(element.name, '');
			}
		}else if($(this).is(':radio')){
			if($(this).is( ':checked' )){
				formData.append(element.name, element.value);
			}
		}else if($(this).is(':file')){
			formData.append(element.name, $(this).prop('files')[0]);
		}else{
			formData.append(element.name, element.value);
		}
	});
	return formData;
}

function generate_json_from_field(selector){
	console.log('generate json from field form');
	var field = $(selector).find( "[name]" );
	var datainput='{';
	var i= 0;
	$(field).each(function(index,element){
		if($(this).is(':checkbox')){
			if($(this).is( ':checked' )){
				datainput += '"'+element.name+'"';
				datainput += ':';
				datainput += '"on"';
				if(i != field.length -1 ){
					datainput += ',';
				}
			}else{
				datainput += '"'+element.name+'"';
				datainput += ':';				
				datainput += '""';
				if(i != field.length -1 ){
					datainput += ',';
				}
			}
		}else if($(this).is(':radio')){
			if($(this).is( ':checked' )){
				datainput += '"'+element.name+'"';
				datainput += ':';
				datainput += '"'+element.value+'"';
				if(i != field.length -1 ){
					datainput += ',';
				}
			}
		}else{
			datainput += '"'+element.name+'"';
			datainput += ':';
			datainput += '"'+element.value+'"';
			if(i != field.length -1 ){
				datainput += ',';
			}
		}		
		i++;
	});
	datainput += '}';
	return datainput;
}

function generate_param_from_field(selector){
	console.log('generate param from field form');
	var field = $(selector).find( "[name]" );
	var datainput='';
	var i= 0;
	$(field).each(function(index,element){
		if($(this).is(':checkbox')){
			if($(this).is( ':checked' )){
				datainput += ''+element.name+'';
				datainput += '=';
				datainput += '"on"';
				if(i != field.length -1 ){
					datainput += '&';
				}
			}else{
				datainput += ''+element.name+'';
				datainput += '=';				
				datainput += '""';
				if(i != field.length -1 ){
					datainput += '&';
				}
			}
		}else if($(this).is(':radio')){
			if($(this).is( ':checked' )){
				datainput += ''+element.name+'';
				datainput += '=';
				datainput += ''+element.value+'';
				if(i != field.length -1 ){
					datainput += '&';
				}
			}
		}else{
			datainput += ''+element.name+'';
			datainput += '=';
			datainput += ''+element.value+'';
			if(i != field.length -1 ){
				datainput += '&';
			}
		}		
		i++;
	});
	return datainput;
}

function generate_form(from_filter){
	console.log('Generate form for frond end');

	if(from_filter == true){
		field_data = the_data.data.filters;
	}else{
		field_data = the_data.data.fields;
	}
	
	var html = '';
	var i;	
	for(i=0;i<field_data.length;i++){
		if(field_data[i].type == 'hidden'){
			html += set_field_form(field_data[i]);
		}else if(field_data[i].type == 'label'){
			if(field_data[i].classes.includes("full-width") == true){
				html += '<div class="form-group col-md-12 col-sm-12 col-xs-12">';
			}else{
				html += '<div class="form-group col-md-6 col-sm-6 col-xs-12">';
			}
			html += '<label><div class="'+text_classes(field_data[i].classes)+'">'+field_data[i].label+'</div></label>';
			html += '<hr style="margin-bottom: 0px !important">';
			html += '</div>';			
		}else if(field_data[i].type == 'separation'){
			if(field_data[i].classes.includes("full-width") == true){
				html += '<div class="col-md-12 col-sm-12 col-xs-12">';
			}else{
				html += '<div class="col-md-6 col-sm-6 col-xs-12">';
			}
			html += '<hr>';
			html += '</div>';			
		}else if(field_data[i].type == 'blank'){
			if(field_data[i].classes.includes("full-width") == true){
				html += '<div class="form-group col-md-12 col-sm-12 col-xs-12">';
			}else{
				html += '<div class="form-group col-md-6 col-sm-6 col-xs-12">';
			}
			html += '<label><h5>.<h5></label>';
			html += '<hr style="margin-bottom: 0px !important">';			
			html += '</div>';			
		}else if(field_data[i].type == 'toogle'){
			if(field_data[i].classes.includes("full-width") == true){
				html += '<div class="form-group col-md-12 col-sm-12 col-xs-12">';
			}else{
				html += '<div class="form-group col-md-6 col-sm-6 col-xs-12">';
			}
			html += set_field_form(field_data[i]);
			html += '</div>';			
		}else{
			if(field_data[i].classes.includes("full-width") == true){
				html += '<div class="form-group '+hidding_field(field_data[i].classes, field_data[i].name)+' col-md-12 col-sm-12 col-xs-12">';
				//html += '<div class="form-group col-md-12 col-sm-12 col-xs-12">';
			}else{
				html += '<div class="form-group '+hidding_field(field_data[i].classes, field_data[i].name)+' col-md-6 col-sm-6 col-xs-12">';
				//html += '<div class="form-group col-md-6 col-sm-6 col-xs-12">';
			}
			html += '<label>'+field_data[i].label+'</label>';
			html += set_field_form(field_data[i]);
			html += '</div>';
		}
	}	
	html += '<div class="clearfix"></div>';
	return html;
}

function set_field_form(data){
	//console.log(data);
	var html = '';
	if(data.type == 'text'){
		html += '<input type="text" name="'+data.name+'" id="'+data.name+'" value="'+data.value+'" class="form-control" placeholder="'+data.placeholder+'" ';					
		html += field_classes(data.classes, data.name);
		html += '>';
	}else if(data.type == 'number'){
		html += '<input type="number" name="'+data.name+'" id="'+data.name+'" value="'+data.value+'" class="form-control" placeholder="'+data.placeholder+'" ';					
		html += field_classes(data.classes, data.name);
		html += '>';
	}else if(data.type == 'email'){
		html += '<input type="email" name="'+data.name+'" id="'+data.name+'" value="'+data.value+'" class="form-control" placeholder="'+data.placeholder+'" ';					
		html += field_classes(data.classes, data.name);
		html += '>';
	}else if(data.type == 'password'){
		html += '<input type="password" name="'+data.name+'" id="'+data.name+'" value="'+data.value+'" class="form-control" placeholder="'+data.placeholder+'" ';					
		html += field_classes(data.classes, data.name);
		html += '>';
	}else if(data.type == 'textarea'){
		html += '<textarea name="'+data.name+'" id="'+data.name+'" class="form-control" rows="4" placeholder="'+data.placeholder+'" ';					
		html += field_classes(data.classes, data.name);
		html += '>'+data.value+'</textarea>';
	}else if(data.type == 'hidden'){
		html += '<input type="hidden" name="'+data.name+'" id="'+data.name+'" value="'+data.value+'" class="form-control" ';	
		html += field_classes(data.classes);
		html += '></textarea>';
	}else if(data.type == 'select'){
		html += '<select name="'+data.name+'" id="'+data.name+'" class="form-control custom-select" ';	
		html += field_classes(data.classes, data.name);
		html += '>';
		if(data.placeholder == ''){
			
		}else{
			html += '<option value="">'+data.placeholder+'</option>';
		}
		var i;
		for(i=0;i<data.options.length;i++){
			//console.log(data.value);
			if(data.value == data.options[i].value){
				html += '<option value="'+data.options[i].value+'" selected>'+data.options[i].label+'</option>';
			}else{
				html += '<option value="'+data.options[i].value+'">'+data.options[i].label+'</option>';
			}
		}
		html += '</select>';
	}else if(data.type == 'radio'){	
		var i;
		for(i=0;i<data.options.length;i++){
			html += '<div class="form-check">';
			html += '<input class="form-check-input" type="radio" name="'+data.name+'" id="'+data.name+i+'" value="'+data.options[i].value+'" ';			
			if(data.value == data.options[i].value){
				html += 'checked>';
			}else{
				html += '>';
			}
			html += '<label class="form-check-label" for="'+data.name+i+'">';
			html += data.options[i].label;
			html += '</label>';
			html += '</div>';
		}
	}else if(data.type == 'checkbox'){	
		var i;
		for(i=0;i<data.options.length;i++){
			html += '<div class="form-check">';
			html += '<input class="form-check-input" type="checkbox" name="'+data.name+'" id="'+data.name+i+'" ';
			if(data.value == 'on'){
				html += 'checked>';
			}else{
				html += '>';
			}
			html += '<label class="form-check-label" for="'+data.name+i+'">';
			html += data.options[i].label;
			html += '</label>';
			html += '</div>';
		}
	}else if(data.type == 'file'){
		html += '<input type="file" name="'+data.name+'" id="'+data.name+'" value="'+data.value+'" class="form-control" placeholder="'+data.placeholder+'" ';					
		html += field_classes(data.classes, data.name);
		html += '>';
	}else if(data.type == 'daterange'){
		html += '<input type="text" name="'+data.name+'" id="'+data.name+'" value="'+data.value+'" class="form-control" ';					
		html += field_classes(data.classes, data.name);
		html += ' />';
	}else if(data.type == 'date'){
		html += '<input type="text" name="'+data.name+'" id="'+data.name+'" value="'+data.value+'" class="form-control" ';					
		html += field_classes(data.classes, data.name);
		html += ' />';
	}else if(data.type == 'info'){
		html += '<div class="">'+data.value+'</div>';
	}else if(data.type == 'toogle'){
		html += '<input type="checkbox" name="'+data.name+'" id="'+data.name+'" value="'+data.value+'" ';					
		html += field_classes(data.classes, data.name);
		if(data.value == 'on'){
			html += 'checked> ';
		}else{
			html += '> ';
		}
		html += data.label;
	}
	return html;
}

function field_classes(value, name){
	var required = '';
	var readonly = '';
	var disabled = '';
	
	if(value.includes("required") == true){
		required = 'required';
	}
	if(value.includes("readonly") == true){
		readonly = 'readonly';
	}
	if(value.includes("disabled") == true){
		disabled = 'disabled';
	}
	if(value.includes("active-when-") == true){
		//$('[name="'+name+'"]').parents('.form-group').show();
		//console.log('true');
		var array_split = value.split(" ");
		array_split.forEach(function(item) {
			if(item.includes("active-when-")){
				var i;
				var item_split = item.substr(12).split('-');
				for(i=0;i<field_data.length;i++){
					if(field_data[i].name == item_split[0]){
						change_select(item_split, name, field_data[i].type, field_data[i].value);
					}
				}
			}
		});
	}
	
	return (required + ' ' + readonly + ' ' + disabled);
}

function hidding_field(value, name){
	var returnClass = 'tes';
	if(value.includes("active-when-") == true){
		var array_split = value.split(" ");
		array_split.forEach(function(item) {
			if(item.includes("active-when-")){
				var i;
				var item_split = item.substr(12).split('-');
				for(i=0;i<field_data.length;i++){
					if(field_data[i].name == item_split[0]){
						//console.log(field_data[i].name);
						if(field_data[i].type == 'toogle' && field_data[i].value == 'on'){
							//console.log('toogle on');
							returnClass =  'displayed';
						}else{
							//console.log('toogle off');
							returnClass =  'hidding';
						}
						if(field_data[i].type == 'select' && field_data[i].value == item_split[1]){
							returnClass =  'displayed';
						}else{
							returnClass =  'hidding';
						}
					}
				}
			}
		});
	}else{
		returnClass =  '';
	}
	//console.log(returnClass);
	return returnClass;
}

function change_select(split, name, type, value){
	//console.log($('#'+name).parent());
	//console.log($('[name="'+name+'"]').parent());
	//console.log()
	if( type == 'select'){
		$(document).on('change', '[name="'+split[0]+'"]', function() {
			if($(this).val() == split[1]){
				//console.log('sama');
				$('[name="'+name+'"]').parent().removeClass('hidding');
				$('[name="'+name+'"]').parent().addClass('displayed');
			}else{
				$('[name="'+name+'"]').parent().removeClass('displayed');
				$('[name="'+name+'"]').parent().addClass('hidding');
			}
		});
	}else if( type  == 'toogle'){
		//console.log(value);
/* 		if(value == 'on'){
			//console.log(value);
			//console.log($('[name="'+name+'"]'));
			$('[name="'+name+'"]').parent().removeClass('hidding');
			$('[name="'+name+'"]').parent().addClass('displayed');			
		} */
		$(document).on('change', '[name="'+split[0]+'"]', function() {
			if(this.checked) {
				//console.log('sama');
				//console.log($('[name="'+name+'"]').parent());
				$('[name="'+name+'"]').parent().removeClass('hidding');
				$('[name="'+name+'"]').parent().addClass('displayed');
			}else{
				$('[name="'+name+'"]').parent().removeClass('displayed');
				$('[name="'+name+'"]').parent().addClass('hidding');
				//$('[name="'+name+'"]').val('');
			}
		});		
	}
}

function init_display(){
	console.log( 'hide display' );
	$(".button-add").hide();
	$(".button-pdf").hide();
	$(".button-xls").hide();
	$(".button-filter").hide();
	$(".tab-filter-first").hide();
	$(".tab-two-content").hide();
	$(".main-table").hide();
}

function hide_toolbar(){
	console.log( 'show or hide toolbar button' );
	if(the_data.type == 'table_two'){
		if(the_data.data.table1.filters != null){
			$(".button-filter1").show();
		}
		if(the_data.data.table2.filters != null){
			$(".button-filter2").show();
		}		
	}	
	
	if(the_data.data.insertable == true){
		$(".button-add").show();
	}else{
		$(".button-add").hide();
	}
	if(the_data.data.pdf == true){
		$(".button-pdf").show();
	}else{
		$(".button-pdf").hide();
	}
	if(the_data.data.xls == true){
		$(".button-xls").show();
	}else{
		$(".button-xls").hide();
	}
	if(the_data.data.filters != null){
		$(".button-filter").show();
	}else{
		$(".button-filter").hide();
	}
	if(the_data.data.toolbars != null){
		var i;
		var html = '';
		for(i=0; i<the_data.data.toolbars.length; i++){
			html += '<div class="button-toolbar-item '+the_data.data.toolbars[i].class+'">';
			html += '<a href="javascript:void(0)" onclick="show_modal(\''+the_data.data.toolbars[i].target+'\')">';
			html += '<i style="font-size: 16px;" class="fa fa-'+the_data.data.toolbars[i].icon+'"></i><br>'+the_data.data.toolbars[i].label;
			html += '</a>';
			html += '</div>';
		}
		$('.top_toolbar .toolbar-right .for_toolbars').html(html);
	}
}

function show_modal(target){
	$.ajax({
		type : "POST",
		url  : target,
		dataType : "JSON",
		success: function(data){
			get_data(data);
		}
	});
	return true;	
}

function generate_modal(){
	var html = '';
	html += '<div class="modal" id="'+the_data.data.id+'" role="dialog">';
	html += '<form id="form-'+the_data.data.id+'" enctype="multipart/form-data" method="post">';
	html += '<div class="modal-dialog modal-lg">';
	html += '<!-- Modal content-->';
	html += '<div class="modal-content">';
	html += '<div class="modal-header">';
	html += '<button type="button" class="close" data-dismiss="modal">&times;</button>';
	html += '<h4 class="modal-title">'+the_data.data.title+'</h4>';
	html += '</div>';
	html += '<div class="modal-body">';

	html += '</div>';
	html += '<div class="modal-footer">';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '</form>';
	html += '</div>';
	return html;
}

function generate_table(datatable){
	console.log('generate table start');
	//console.log(datatable);
	var html = '';
	html += '<div class="table-responsive '+set_fixed_table(the_data.data.classes)+'">';
	html += '<table class="table'+table_classes(the_data.data.classes)+'">';
	html += '<thead>';
	html += set_table_header(datatable.header);
	html += '</thead>';
	html += '<tbody>';
	html += set_table_body(datatable.body);
	if(datatable.footer != null){
		html += set_table_footer(datatable.footer);
	}
	html += '</tbody>';
	html += '</table>';
	html += '</div>';
	console.log('generate table finished');
	return html;
}

function set_fixed_table(value){
	var returnvalue = ''
	if(value.includes("fixed-") == true){
		var array_split = value.split(" ");
		array_split.forEach(function(item) {
			if(item.includes("fixed-")){
				var i;
				var item_split = item.substr(6).split('-');
				console.log(item_split[0]);
				console.log($('.table-fixing'));
				$('.table-fixing').height(item_split[0]);
			}
		});
		returnvalue = 'table-fixing'
	}else{
		returnvalue = '';
	}
	return returnvalue;
}

function set_table_header(header){
	console.log('set table header');
	var html = '';
	var i;
	var j;
	for(i=0; i<header.length; i++){
		html += '<tr>';
		for(j=0; j<header[i].length; j++){
			html += '<td class="align-middle '+ text_classes(header[i][j].classes)+ '" ';     
			if(header[i][j].rowspan != null){
				html += 'rowspan="'+header[i][j].rowspan+'" ';	
			}
			if(header[i][j].colspan != null){
				html += 'colspan="'+header[i][j].colspan+'" ';	
			}							
			html += '>';
			html += header[i][j].value;
			html += '</td>';
		}
		if(the_data.data.editable == true || the_data.data.deletable == true || the_data.data.statusable == true || the_data.data.detailable == true){
			if(i==0){
				html += '<td class="align-middle text-center font-weight-bold" rowspan="'+header.length+'" colspan="4">Action';
				html += '</td>'; 
			}
		}
		html += '</tr>';
	}
	return html;
}

function set_table_body(body){
	console.log('set table body');
	var html = '';
	var i;
	var j;
	for(i=0; i<body.length; i++){
		html += '<tr>';
		for(j=0; j<body[i].length; j++){
			if(body[i][j].classes.includes("hidden") == true){
				
			}else{
				html += '<td class="'+ text_classes(body[i][j].classes) +'"';
				if(body[i][j].rowspan != null){
					html += 'rowspan="'+body[i][j].rowspan+'" ';	
				}
				if(body[i][j].colspan != null){
					html += 'colspan="'+body[i][j].colspan+'" ';	
				}
				html += '>';
				html += body[i][j].value;
				html += '</td>';
			}
		}
		if(body.length == 1 & body[i][0].classes.includes("empty") == true){
			
		}else{
			if(the_data.data.statusable == true){
				html += '<td class="text-center font-weight-bold">';
				html += '<a class="item-status" id="'+body[i][0].value+'" href="javascript:void(0);" title="edit status"><i style="font-size: 16px;" class="fa fa-check"></i></a>';
				html += '</td>';
			}					
			if(the_data.data.editable == true){
				html += '<td class="text-center font-weight-bold">';
				html += '<a class="item-edit" id="'+body[i][0].value+'" href="javascript:void(0);" title="edit"><i style="font-size: 16px;" class="fa fa-edit"></i></a>';
				html += '</td>';
			}
			if(the_data.data.deletable == true){
				html += '<td class="text-center font-weight-bold">';
				html += '<a class="item-delete" id="'+body[i][0].value+'" href="javascript:void(0);" title="delete"><i style="font-size: 16px;" class="fa fa-trash"></i></a>';
				html += '</td>';
			}
			if(the_data.data.detailable == true){
				html += '<td class="text-center font-weight-bold">';
				html += '<a class="item-detail" id="'+body[i][0].value+'" href="javascript:void(0);" title="detail"><i style="font-size: 16px;" class="fa fa-info"></i></a>';
				html += '</td>';
			}			
		}					
		html += '</tr>';
	}
	return html;
}

function set_table_footer(footer){
	console.log('set table footer');
	var html = '';
	var i;
	var j;
	for(i=0; i<footer.length; i++){
		html += '<tr>';
		for(j=0; j<footer[i].length; j++){
			html += '<td class="align-middle '+ text_classes(footer[i][j].classes)+ '" ';     
			if(footer[i][j].rowspan != null){
				html += 'rowspan="'+footer[i][j].rowspan+'" ';	
			}
			if(footer[i][j].colspan != null){
				html += 'colspan="'+footer[i][j].colspan+'" ';	
			}							
			html += '>';
			html += footer[i][j].value;
			html += '</td>';
		}
		if(the_data.data.editable == true || the_data.data.deletable == true || the_data.data.statusable == true || the_data.data.detailable == true){
			if(i==0){
				html += '<td class="align-middle text-center font-weight-bold" rowspan="'+footer.length+'" colspan="4">';
				html += '</td>'; 
			}
		}
		html += '</tr>';
	}
	return html;
}

function table_classes(value){
	var table='';
	if(value.includes("striped") == true){
		table += ' table-striped';
	}
	if(value.includes("bordered") == true){
		table += ' table-bordered';
	}
	if(value.includes("hover") == true){
		table += ' table-hover';
	}
	return table;
}

function text_classes(value){
	var fontweight;
	var fontitalic;
	var texttransform;
	var textalign;
	var textcolor;
	
	if(value.includes("bold") == true){
		fontweight= 'font-weight-bold';
	}else if(value.includes("light") == true){
		fontweight= 'font-weight-light';
	}else if(value.includes("normal") == true){
		fontweight= 'font-weight-normal';
	}else{
		fontweight= 'font-weight-normal';
	}
	
	if(value.includes("italic") == true){
		fontitalic= 'font-italic';
	}else{
		fontitalic= '';
	}
	
	if(value.includes("uppercase") == true){
		texttransform= 'text-uppercase';
	}else if(value.includes("lowercase") == true){
		texttransform= 'text-lowercase';
	}else if(value.includes("capitalize") == true){
		texttransform= 'text-capitalize';
	}else{
		texttransform= '';
	}			
	
	if(value.includes("align-left") == true){
		textalign= 'text-left';
	}else if(value.includes("align-center") == true){
		textalign= 'text-center';
	}else if(value.includes("align-right") == true){
		textalign= 'text-right';
	}else{
		textalign= 'text-left';
	}
	
	if(value.includes("warning") == true){
		textcolor= 'text-warning';
	}else if(value.includes("info") == true){
		textcolor= 'text-info';
	}else if(value.includes("danger") == true){
		textcolor= 'text-danger';
	}else if(value.includes("success") == true){
		textcolor= 'text-success';
	}else if(value.includes("primary") == true){
		textcolor= 'text-primary';
	}	

	return (fontweight+ ' ' + fontitalic + ' ' + texttransform + ' ' + textalign + ' ' + textcolor);
}

