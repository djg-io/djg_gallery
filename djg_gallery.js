function showAlert(text,status) {
	var color = 'black';
	if(status=='error'){
		color = 'red';
	}else if(status=='alert'){
		color = 'orange';
	}else if(status=='ok'){
		color = 'green';
	};
	 var currentTime = new Date();
	$('.djg_dialog_window').prepend('<p>' + currentTime.getHours() + ':' + currentTime.getMinutes() + ':' + currentTime.getSeconds() + ' | <span style="color:'+color+';">'+text+'</span></p>');
	$('.djg_dialog_window').animate({scrollTop:0}, 'slow');
};