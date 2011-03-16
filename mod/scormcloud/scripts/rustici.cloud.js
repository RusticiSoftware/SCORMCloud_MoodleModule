$(document).ready(function(){

	//alert('jquery works');
	$("#lnkPackageProperties").click(function(){
		
		//alert('show package props control');
		
	});
	

	
});

function BuildRegistrationDetail(regxml){
	
	_output = "";
	
	_learner_name = "";
	_title = "";
	
	_learner_name = $(regxml).find('learner_name').text();
	$(regxml).find('title').each(function(){
		_title = $(this).text();
	});
	
	
	_output = _learner_name;
	_output += '<br>';
	_output += _title;
	alert(_output);
	$("#RegDetails").text(_output);
	
	
}