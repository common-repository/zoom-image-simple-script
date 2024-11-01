function _ziss_submit() {
	if(document.ziss_form.ziss_img_sm.value == "") {
		alert(ziss_adminscripts.ziss_image);
		document.ziss_form.ziss_img_sm.focus();
		return false;
	}
	else if(document.ziss_form.ziss_img_bg.value == "") {
		alert(ziss_adminscripts.ziss_image);
		document.ziss_form.ziss_img_bg.focus();
		return false;
	}
	else if(document.ziss_form.ziss_title.value == "") {
		alert(ziss_adminscripts.ziss_title);
		document.ziss_form.ziss_title.focus();
		return false;
	}
	else if(document.ziss_form.ziss_group.value == "" && document.ziss_form.ziss_group_txt.value == "") {
		alert(ziss_adminscripts.ziss_group);
		document.ziss_form.ziss_group.focus();
		return false;
	}
}

function _ziss_delete(id) {
	if(confirm(ziss_adminscripts.ziss_delete)) {
		document.frm_ziss_display.action="options-general.php?page=zoom-image-simple-script&ac=del&did="+id;
		document.frm_ziss_display.submit();
	}
}	

function _ziss_redirect() {
	window.location = "options-general.php?page=zoom-image-simple-script";
}

function _ziss_help() {
	window.open("http://www.gopiplus.com/work/2021/03/21/zoom-image-wordpress-plugin/");
}

function _ziss_numericandtext(inputtxt) {  
	var numbers = /^[0-9a-zA-Z]+$/;  
	document.getElementById('ziss_group').value = "";
	if(inputtxt.value.match(numbers)) {  
		return true;  
	}  
	else {  
		alert(ziss_adminscripts.ziss_numletters); 
		newinputtxt = inputtxt.value.substring(0, inputtxt.value.length - 1);
		document.getElementById('ziss_group_txt').value = newinputtxt;
		return false;  
	}  
}