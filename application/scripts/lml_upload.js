$(document).ready(function(){
	$(window).load(function(){
		if($("#file_url").length > 0){
			var params = {
					file_url: 		$("#file_url").val(),
					file_path: 		$("#file_path").val(),
					file_width: 	$("#file_width").val(),
					file_height: 	$("#file_height").val()
			};
			parent.lml.load_img(params);
		}
	});
	
	$("#upload_img").change(function(){
		parent.$(".upload_pst_loader").css("display", "block");
		$("#btn_upload").click();
	});
	
	//error
	if($("#error_ms").length > 0){
		parent.lml.error_load_img();
	}
});
