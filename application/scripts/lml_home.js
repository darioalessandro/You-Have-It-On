$(document).ready(function(){
	/*$("#log_email").inputLabel();
	$("#log_password").inputLabel();*/
	
	$(".link_face").click(function(){
		$(this).css("display", "none");
		$(".loader_linface").css("display", "block");
	});
	
	$("#frmRegister").submit(function(){
		var vthis = this,
		name = $("#rg_name").val(),
		sex = $("#rg_sex").val(),
		email = $("#rg_email").val(),
		password = $.trim($("#rg_password").val());
		password = password.length>0? calcMD5(password): '';
		var confirm_password = $.trim($("#rg_password2").val());
		confirm_password = confirm_password.length>0? calcMD5(confirm_password): '';
		
		var preloader = $("#preloader_rg");
		preloader.html('<img src="'+$$.conf.url_base+'application/images/loader_min.gif" width="16" height="16">');
		$$.user_register({
			lang: 				$$.conf.lang,
			consumer_key: 		$$.conf.consumer_key,
			consumer_secret:	$$.conf.cssct,
			name:				name,
			sex: 				sex,
			email: 				email,
			password: 			password,
			confirm_password:	confirm_password,
			callback: function(data){
				preloader.html("");
				if(data.status.code == 200){
					$("input[type='text'], input[type='password']", vthis).val("");
				}
				preloader.html("<span>"+data.status.message+"</span>");
			}
		});
		return false;
	});
	
	$("#slider").easySlider({
		auto: true,
		continuous: true,
		numeric: false, 
		pause: 5000
	});
});

/*$(window).load(function(){
    $('#slider').nivoSlider({
        effect:'sliceDown',
        pauseTime:6000
    });
});*/