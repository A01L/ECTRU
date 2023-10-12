function include(file) {
	var script = document.createElement('script');
	script.src = file;
	script.type = 'text/javascript';
	script.defer = true;
	document.getElementsByTagName('head').item(0).appendChild(script);
}
include('ectr/jquery.min.js');

function get_value_input(attr, key) {
    return document.querySelector("input[" + attr + "='" + key + "']").value;
}

function put_data_id(id, data) {
    document.getElementById(id).innerHTML = data;
}

function put_value_id(id, data) {
    document.getElementById(id).value = data;
}

function put_src_id(id, data) {
    document.getElementById(id).src = data;
}

function ssp_data(url, form) {
    // заполним FormData данными из формы (document.forms.FORM_NAME)
    let formData = new FormData(document.forms[form]);


    let xhr = new XMLHttpRequest();
    xhr.open("POST", url);
    xhr.send(formData);
}

function sendAjaxForm(ajax_form, url) {
	$.ajax({
		url: url, 
		type: "POST", 
		dataType: "html", 
		data: $("#" + ajax_form).serialize(),  
		success: function (response) { 
			result = $.parseJSON(response);
			return result;
		},
		error: function (response) { 
			$('#result_form').html('Ошибка. Данные не отправлены.');
		}
	});
};

function jq_post(btn,form, url) {
	$(document).ready(function () {
		$('#' + btn).click(
			function () {
				sendAjaxForm(form, url);
				return false;
			}
		);
	});
}

