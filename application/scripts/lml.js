$(document).ready(function(){
	/**
	 * Agregar postyle
	 */
	$("#frm_add_postyle").submit(function(){
		return false;
	});
	
	$("#msgPostyle").inputLabel();
	
	ccomments.ini();
	crankear.ini();
	cpostyles.ini();
	cfriends.ini();
	citems.ini();
	cTabControl.ini();
	cBrand.ini();
	cStore.ini();
	cMiniItems.ini();
	cOcation.ini();
	cNotification.ini();
	
	timezone.ini();
});

$(window).load(function(){
	if($("#import_fbcontacts").length > 0){
		$$.facebook_importFriends({
			lang:			$$.conf.lang,
			consumer_key:	$$.conf.consumer_key,
			oauth_token:	$$.conf.oauth_token,
			callback: function(data){
				window.location = $$.conf.url_base+"?lang="+$$.conf.lang;
			}
		});
	}
});



function WindowsModalControl(){
	this.selector_obj	= undefined;
	this.title_win		= "";
	this.width_win		= 560;
	this.height_win		= 300;
}
WindowsModalControl.prototype.setVals = function(obj){
	try{
		//for(var item = 0; item < obj.length; item++){
		for(var item in obj){
			this[item] = obj[item];
		}
		
		this.renderWindows();
	}catch(e){}
};
WindowsModalControl.prototype.renderWindows = function(){
	var vthis = this;
	$(this.selector_obj).dialog({
		modal: true,
		resizable: false,
		title: vthis.title_win,
		width: vthis.width_win,
		height: vthis.height_win,
		autoOpen: true,
		close: function(event, ui){
			$(event.target).dialog("destroy");
			$(vthis.selector_obj).remove();
		},
		create: function(event, ui){
			setTimeout(function(){
				$(".ui-widget-overlay").click(function(){
					$(event.target).dialog("close");
				});
			}, 400);
		}
	});
};

var WindowsModalControlUtil = {
	list_objs : ['item_detail', 'store_detail', 'brand_detail'],
	clearWindowsModalControl: function(){
		var objwin, obs;
		
		for(objwin in this.list_objs){
			obs = $("#"+this.list_objs[objwin]);
			if(obs.length > 0){
				obs.dialog("destroy");
				obs.remove();
			}
		}
	}
};


/**
 * Gallery control events
 * @param value
 * @returns {GalleryEvents}
 */
function GalleryEvents(value){
	this.value = value;
	this.barControl_click = this.createAdvancedClickHandler(value);
}
GalleryEvents.prototype.createAdvancedClickHandler = function _MyEvents_createAdvancedClickHandler(value){
	return function(event){
		GalleryEvents.barControl_click(event || window.event, value);
	};
};
GalleryEvents.barControl_click = function _MyEvents_advancedClickHandler(event, value){
	if(event.type == 'load')
		value.loadImg(event, value);
	else if(event.type == 'click')
		value.gotoPage_click(event, value);
};
/**
 * GALLERY CONTROL
 * @returns {ImageGalleryControl}
 */
function ImageGalleryControl(){
	this.items		= null;
	this.num_items	= null;
	this.insert_in	= null;
	this.paginar	= new pagination();
	
	this.ident		= Math.floor(Math.random()*101);
	this.width_item	= 200;
	this.width_border	= 2;
	this.height_gallery = 400;
	
	this.events 	= new GalleryEvents(this);
}
ImageGalleryControl.prototype.setVals = function(obj){
	try{
		//for(var item = 0; item < obj.length; item++){
		for(var item in obj){
			this[item] = obj[item];
		}
		
		var response = this.renderGallery();
		
		if(response!=undefined)
			return response;
		else
			$(".pag_gallery", $(this.insert_in)).click(this.events.barControl_click);
		//$(".pag_gallery", $(this.insert_in)).live("click", this.events.barControl_click);
	}catch(e){}
};
ImageGalleryControl.prototype.getNumItems = function(){
	return (this.num_items!=null? this.num_items: this.items.length);
};
ImageGalleryControl.prototype.renderGallery = function(){
	if(this.num_items==null){
		var html = '<div class="ui_gallery" style="width: '+(this.width_item+this.width_border)+'px;">'+
			'<div class="uigary_modal" style="width: '+((this.width_item+2+this.width_border)*this.getNumItems())+'px; height:'+this.width_item+'px;">',
		img = undefined, item;
		
		this.paginar.setVals({
			total_rows 		: this.getNumItems(),
			current_page	: '1',
			per_page		: '1',
			pag_class		: 'pag_gallery',
			return_html		: true
		});
		
		//for(var item = 0; item < this.items.length; item++){
		for(item in this.items){
			/*img = new Image();
			img.src = this.items[item]["sizes"][1]["url"];
			img.id = 'img_gallery'+this.ident+item;
			img.onload = this.events.barControl_click; //ImageGalleryControl.prototype.loadImg;*/
			html += '<span class="uigary_item" style="width:'+this.width_item+'px; height:'+this.width_item+'px;"><img id="img_gallery'+
				this.ident+item+'" src="'+this.items[item]["sizes"][1]["url"]+'"></span>';
		}
		html +=	'<div class="clear"></div></div>'+
		'<div class="clear"></div>'+
		'<div id="ui_pagination_gall'+this.ident+'" class="ui_pagination"><div class="pages">'+
			this.paginar.render()+'</div><div class="clear"></div></div>'+
		'</div>';
		
		if(this.insert_in == null){ //cuando se regresa el html
			return html;
		}else{ //cuando se inserta en algun elemento espesifico
			$(this.insert_in).html(html);
			$('img[id^="img_gallery'+this.ident+'"]').load(this.events.barControl_click);
		}
	}else{	//cuando se pinta el html desde php
		$('img[id^="img_gallery"]').load(this.events.barControl_click);
	}
	
};
ImageGalleryControl.prototype.gotoPage_click = function(event, value){
	var vthis	= event.target,
	pgallery	= $(event.target).parents("div.ui_gallery"),
	pag			= $(event.target).attr("rel"),
	peffect = new effects();
	
	value.paginar.setVals({
		total_rows 		: value.getNumItems(),
		current_page	: pag,
		per_page		: '1',
		pag_class		: 'pag_gallery',
		obj_id			: 'ui_pagination_gall'+value.ident+' .pages',
		return_html		: false
	});
	value.paginar.render();
	$(".pag_gallery", $('#ui_pagination_gall'+value.ident)).click(this.events.barControl_click);
	
	peffect.gallery({
		width_item	: value.width_item+value.width_border,
		pfriends	: $("div.uigary_modal", pgallery),
		vthis		: vthis
	});
};
ImageGalleryControl.prototype.loadImg = function(event, value){
	var twidth, theight,
	target = getTarget(event);
	
	try{
		twidth = target.width;
		theight = target.height;
	}catch(e){
		twidth = 180;
		theight = 170;
	}
	if(twidth < theight){
		var pro = value.width_item*100/theight;
		$("#"+target.id).attr("width", parseInt(pro*twidth/100));
		$("#"+target.id).attr("height", value.width_item);
	}else{
		var pro = value.width_item*100/twidth,
		height_calc = parseInt(pro*theight/100), margin;
		$("#"+target.id).attr("height", height_calc);
		$("#"+target.id).attr("width", value.width_item);
		
		margin = parseInt((value.width_item-height_calc)/2);
		$("#"+target.id).css("margin-top", margin+"px");
	}
	
};

function getTarget(evt){
	 evt = evt || window.event;
	 var targetElement = evt.target || evt.srcElement;
	 
	 if (targetElement.nodeName.toLowerCase() == 'li'){
		 return targetElement;
	 }else if (targetElement.parentNode.nodeName.toLowerCase() == 'li'){
		 return targetElement.parentNode;
	 }else{
		 return targetElement;
	 }
}

/**
 * Clase paginacion
 * @param rows
 * @param page
 * @param clase
 * @param href
 * @returns {pagination}
 */
function pagination(rows, page, x_page, clase, href, id_obj){
	this.obj_id			= id_obj?id_obj:'ui_pagination';
	this.total_rows 	= parseInt(rows);
	this.current_page	= parseInt(page);
	this.per_page		= parseInt(x_page);
	this.pag_class		= clase?clase:'';
	this.href			= href?href:'javascript:void(0);';
	
	this.first_link		= false;
	this.last_link		= false;
	this.next_link		= true;
	this.prev_link		= true;
	this.number_link	= false;
	this.return_html	= false;
}
pagination.prototype.setVals = function(obj){
	try{
		//for(var item = 0; item < obj.length; item++){
		for(var item in obj){
			/*if(typeof obj[item] == 'string')
				eval("this."+item+" = '"+obj[item]+"'");
			else*/
			this[item] = obj[item];
		}
	}catch(e){}
};
pagination.prototype.setVal = function(name, val){
	try{
		eval("this."+name+" = "+val);
	}catch(e){}
};
pagination.prototype.getVal = function(name){
	return eval("this."+name);
};
pagination.prototype.render = function(){
	var pages = Math.ceil(this.total_rows / this.per_page),
	html  = '', i;
	
	if(this.current_page > 1 && this.first_link)
		html += '&nbsp;<a class="ui_pag_link '+this.pag_class+'" href="'+this.href+'" rel="1">‹ '+lang.first+'</a>';
	if(this.current_page > 1 && this.prev_link)
		html += '&nbsp;<a class="previous ui_pag_link '+this.pag_class+'" href="'+this.href+'" rel="'+(this.current_page - 1)+'"><</a>';
	if(this.number_link){
		for(i=1; i<=pages;i++){
			if(i == this.current_page){
				html += '&nbsp;<strong>'+i+'</strong>';
			}else{
				html += '&nbsp;<a class="ui_pag_link '+this.pag_class+'" href="'+this.href+'" rel="'+i+'">'+i+'</a>';
			}
		}
	}
	if(this.current_page < pages && this.next_link){
		html += '&nbsp;<a class="next ui_pag_link '+this.pag_class+'" href="'+this.href+'" rel="'+(parseInt(this.current_page) + 1)+'">></a>';
	}
	if(this.current_page < pages && this.last_link)
		html += '&nbsp;<a class="ui_pag_link '+this.pag_class+'" href="'+this.href+'" rel="'+pages+'">'+lang.last+' ›</a>';
	
	if(this.return_html)
		return html;
	else
		$("#"+this.obj_id).html(html);
};




/**
 * Control Brand
 */
var cBrand = {	
	ini: function(){
		
		//evento para la ventana preview
		$(".preview_brand").live('click', cBrand.openWinPreview_click);
	},
	
	openWinPreview_click: function(event){
		var pcon_item, brand_id, me_id, paramsj;
		if($(event).attr("obj_type") == 'brand'){
			brand_id = $(event).attr("obj_id"),
			me_id = $(event).attr("me_id");
		}else{
			pcon_item	= $(this).parents("div.ui_item");
			brand_id = $(this).attr("rel");
			me_id = $("#me_id", pcon_item).val();
			
			if(brand_id == undefined)
				brand_id = $(this).attr("rel");
			
			if($(this).is(".close")){
				$("#"+$(this).attr("close")).dialog("destroy");
				$("#"+$(this).attr("close")).remove();
				me_id = $(this).attr("me_id");
			}else if(me_id==undefined)
				me_id = $(this).attr("me_id");
			event.stopPropagation();
		}
		
		paramsj = {
			lang:					$$.conf.lang,
			consumer_key:			$$.conf.consumer_key,
			result_items_per_page:	'20',
			result_page:			'1',
			filter_brand_id:		brand_id,
			return_comments:		'0',
			callback: function(data){
				if(data.status.code == 200){
					WindowsModalControlUtil.clearWindowsModalControl();
					
					var gallery = new ImageGalleryControl(), pos_like='',
					html_gall =  gallery.setVals({
						items		: data.brands[0].images,
						width_item	: 180
					}),
					
					html_store = '', cate;
					//for(var cate = 0; cate < data.brands[0].categorys.length; cate++){
					for(cate in data.brands[0].categorys){
						html_store += ', '+data.brands[0].categorys[cate].name;
					}
					html_store = html_store.substr(2);
					html_store = '<div class="itm_con_data"><strong>'+lang.products+':</strong> '+html_store+'</div>';
					
					
					var html_charac = '';
					html_charac += '<div class="brand_con_subdata"><strong>'+lang.description+':</strong> <span>&nbsp;'+
					data.brands[0].description+'</span></div>';
					
					html_charac += (data.brands[0].headquarters.length>0? 
						'<div class="brand_con_subdata"><strong>'+lang.located_in+':</strong> <span>&nbsp;'+data.brands[0].headquarters[0].city+
						', '+data.brands[0].headquarters[0].state+', '+data.brands[0].headquarters[0].country+'</span></div>': '');
					
					html_charac += (data.brands[0].websites.length>0? 
							'<div class="brand_con_subdata"><strong>'+lang.websites+':</strong> <span>&nbsp;<a href="'+data.brands[0].websites[0].url+'" rel="nofollow">'+
							data.brands[0].websites[0].url+'</a></span></div>': '');
					
					var like_sel = '', unlike_sel	= '', html_like = '';
					if(me_id != '' && me_id != undefined){
						if(data.brands[0].images.length < 2)
							pos_like = ' style="top:0;"';
						
						if(data.brands[0].like=="1")
							like_sel 	= ' ui_likes_sel';
						else if(data.brands[0].like=="-1")
							unlike_sel 	= ' ui_likes_sel';
						html_like = '<div class="ui_likes"'+pos_like+'>'+
						'				<a href="javascript:void(0);" class="ui_like like'+like_sel+'">'+lang.like+'</a>'+
						'				<a href="javascript:void(0);" class="ui_like unlike'+unlike_sel+'">'+lang.unlike+'</a>'+
						'			</div>';
					}
					
					var html = '<div id="brand_detail">'+
					'	<div class="itm_detail_conte" style="border:none;">'+
					'		<div class="itm_conte_left" style="width: 180px;">'+html_gall+
					'			<div class="clear"></div>'+
									html_like+
					'		</div>'+
					'		<div class="itm_conte_right" style="width: 305px;">'+
					'			<div class="itm_con_data"><strong>'+lang.name+':</strong> '+data.brands[0].name+'</div>'+
								html_store+
					'			<div class="itm_con_data"><strong>'+lang.details+'</strong>'+
					'				<div class="clear"></div>'+
									html_charac+
					'			</div>'+
					'		</div>'+
					'		<div class="clear"></div>'+
					'	</div>'+
					'	<div class="clear"></div>'+
					'	<input type="hidden" id="brand_id" value="'+data.brands[0].id+'">'+
					'	<input type="hidden" id="me_id" value="'+me_id+'">'+
					'</div>';
					$("body").append(html);
					$('img[id^="img_gallery"]', $("#brand_detail")).load(gallery.events.barControl_click); //ajustar imagenes de galeria
					$(".pag_gallery", $("#brand_detail")).click(gallery.events.barControl_click); //evento de la galeria
					
					if(me_id != '' && me_id != undefined){
						var like_win = new clikes("div#brand_detail", "#brand_id", "#me_id", "user_like_brand", "brand_id");
						$("#brand_detail .ui_like").live('click', like_win.events.barControl_click);
					}
					
					//creamos el windows control
					var win = new WindowsModalControl();
					win.setVals({
						selector_obj: "#brand_detail",
						title_win: data.brands[0].name,
						width_win: 525,
						height_win: $("#brand_detail").height()+51
					});
				}
			}
		};
		if($$.conf.oauth_token != '')
			paramsj["oauth_token"] = $$.conf.oauth_token;
		$$.brand_query(paramsj);
		
	},
	
	
	
	asigAutocomplete: function(){
		$('input[id^="brand_aucomplete"]').each(function(){
			var auto = new autocomplete(this, {
				url_service: $$.conf.url_base_service+"brand/query.json",
				name_array: "brands",
				field_filter: "filter_name",
				field_par_name: "name",
				call_in_enter: true,
				call_enter_all: true,
				callback: function(data){
					if(data == "sel_enter"){
						if($("#miniItmDetal_show").is(".yes")){
							$("#btn_publish_tag").focus(); //click();
						}else
							$("#miniAddItem_price").focus();
					}else if(data == "no_result"){
						//citems.showMiniFrmAddItem();
					}
				}
			},{
				lang:					$$.conf.lang,
				consumer_key:			$$.conf.consumer_key,
				oauth_token:			$$.conf.oauth_token,
				result_items_per_page:	'10',
				result_page:			'1',
				filter_name:			''
			});
		});
	}
};




/**
 * Control Store
 */
var cStore = {	
	ini: function(){
		$("#btn_selLocation").click(cStore.selLocation_click);
	
		//evento para la ventana preview
		$(".preview_store").live('click', cStore.openWinPreview_click);
	},
	
	openWinPreview_click: function(event){
		var pcon_item, store_id, me_id, paramsj;
		if($(event).attr("obj_type") == 'store'){
			store_id = $(event).attr("obj_id"),
			me_id = $(event).attr("me_id");
		}else{
			pcon_item	= $(this).parents("div.ui_item");
			store_id = $("#store_id", pcon_item).val();
			me_id = $("#me_id", pcon_item).val();
			
			if(store_id == undefined)
				store_id = $(this).attr("rel");
			
			if($(this).is(".close")){
				$("#"+$(this).attr("close")).dialog("destroy");
				$("#"+$(this).attr("close")).remove();
				me_id = $(this).attr("me_id");
			}
			event.stopPropagation();
		}
		
		paramsj = {
			lang:					$$.conf.lang,
			consumer_key:			$$.conf.consumer_key,
			result_items_per_page:	'20',
			result_page:			'1',
			filter_store_location_id: store_id,
			return_comments:		'0',
			callback: function(data){
				if(data.status.code == 200){
					WindowsModalControlUtil.clearWindowsModalControl();
					
					var gallery = new ImageGalleryControl(), pos_like='',
					html_gall =  gallery.setVals({
						items		: data.stores[0].images,
						width_item	: 180
					}), html_charac, direc, google_dire, 
					
					html_store = (data.stores[0].brand.length==undefined? 
						'<div class="itm_con_data"><strong>'+lang.brand+':</strong> <a href="javascript:void(0);" class="link_blue preview_brand close" rel="'+
						data.stores[0].brand.id+'" close="store_detail" me_id="'+me_id+'">'+data.stores[0].brand.name+'</a></div>': '');
					
					direc = (data.stores[0].street_name!=null? data.stores[0].street_name: '')+
						(data.stores[0].city!=null && data.stores[0].city!=''?', '+data.stores[0].city: '')+
						(data.stores[0].state!=null && data.stores[0].state!=''? ', '+data.stores[0].state: '')+
						(data.stores[0].country!=null && data.stores[0].country!=''? ', '+data.stores[0].country: '');
					
					html_charac = '<div class="itm_con_subdata"><strong>'+lang.address+':</strong> <span>&nbsp;'+direc+'</span></div>';
					
					if(data.stores[0].phone_numbers.length>0){
						var tels = '', tel;
						//for(var tel = 0; tel < data.stores[0].phone_numbers.length; tel++){
						for(tel in data.stores[0].phone_numbers){
							tels += "<br>&nbsp;&nbsp;&nbsp;&nbsp;"+data.stores[0].phone_numbers[tel].phone;
						}
						html_charac += 
						'<div class="itm_con_subdata"><strong>'+lang.phones+':</strong> <span>&nbsp;'+tels+'</span></div>';
					}
					
					html_charac += (data.stores[0].websites.length>0? 
							'<br><div class="brand_con_subdata"><strong>'+lang.websites+':</strong> <span>&nbsp;<a href="'+data.stores[0].websites[0].url+'" rel="nofollow">'+
							data.stores[0].websites[0].url+'</a></span></div>': '');
					
					//mapa google
					html_charac += '<br><div class="clear"></div><div class="brand_con_subdata"><strong><a href="http://maps.google.com/maps?q='+
						data.stores[0].lat+','+data.stores[0].lon+'&z=16" rel="nofollow" target="_blank" class="link_blue" style="font-weight:bold;">'+
						lang.view_map+'</a></strong></div>';
					
					var like_sel = '', unlike_sel	= '', html_like = '';
					if(me_id != '' && me_id != undefined){
						if(data.stores[0].images.length < 2)
							pos_like = ' style="top:0;"';
						
						if(data.stores[0].like=="1")
							like_sel 	= ' ui_likes_sel';
						else if(data.stores[0].like=="-1")
							unlike_sel 	= ' ui_likes_sel';
						html_like = '<div class="ui_likes"'+pos_like+'>'+
						'				<a href="javascript:void(0);" class="ui_like like'+like_sel+'">'+lang.like+'</a>'+
						'				<a href="javascript:void(0);" class="ui_like unlike'+unlike_sel+'">'+lang.unlike+'</a>'+
						'			</div>';
					}
					
					var html = '<div id="store_detail">'+
					'	<div class="itm_detail_conte" style="border:none;">'+
					'		<div class="itm_conte_left" style="width: 180px;">'+html_gall+
					'			<div class="clear"></div>'+
								html_like+
					'		</div>'+
					'		<div class="itm_conte_right" style="width: 305px;">'+
					'			<div class="itm_con_data"><strong>'+lang.name+':</strong> '+data.stores[0].name+'</div>'+
								html_store+
					'			<div class="itm_con_data"><strong>'+lang.details+'</strong>'+
					'				<div class="clear"></div>'+
									html_charac+
					'			</div>'+
					'		</div>'+
					'		<div class="clear"></div>'+
					'	</div>'+
					'	<div class="clear"></div>'+
					'	<input type="hidden" id="store_id" value="'+data.stores[0].id+'">'+
					'	<input type="hidden" id="me_id" value="'+me_id+'">'+
					'</div>';
					$("body").append(html);
					$('img[id^="img_gallery"]', $("#store_detail")).load(gallery.events.barControl_click); //ajustar imagenes de galeria
					$(".pag_gallery", $("#store_detail")).click(gallery.events.barControl_click); //evento de la galeria
					
					if(me_id != '' && me_id != undefined){
						var like_win = new clikes("div#store_detail", "#store_id", "#me_id", "user_like_store", "store_id");
						$("#store_detail .ui_like").live('click', like_win.events.barControl_click);
					}
					
					//creamos el windows control
					var win = new WindowsModalControl();
					win.setVals({
						selector_obj: "#store_detail",
						title_win: data.stores[0].name,
						width_win: 525,
						height_win: $("#store_detail").height()+49
					});
				}
			}
		};
		if($$.conf.oauth_token != '')
			paramsj["oauth_token"] = $$.conf.oauth_token;
		$$.store_query(paramsj);
		
	},
	
	
	
	selLocation_click: function(){
		var latlng = marker.getPosition();
		$("#miniAddItem_store").val("");
		$("#miniAddItem_store_lat").val(latlng.lat());
		$("#miniAddItem_store_lon").val(latlng.lng());
		$("#conte_mapa").css("top", "-1000px");
	},
	
	asigAutocomplete: function(){
		$('input[id^="store_aucomplete"]').each(function(){
			var auto = new autocomplete(this, {
				url_service: $$.conf.url_base_service+"store/query.json",
				name_array: "stores",
				field_filter: "filter_name",
				field_par_name: "name",
				call_in_enter: true,
				call_on_result: true,
				callback: function(data){
					if(data == "sel_enter"){
						$("#btn_publish_tag").click();
					}else if(data == "no_result"){
						if($(auto.proto.obj_intput).val() != ''){
							var res = getPos(document.getElementById("store_aucomplete11"));
							if(res.top > 0){
								var obj_map = $("#conte_mapa");
								obj_map.css("top", res.top+"px");
								obj_map.css("left", res.left+"px");
							}
						}else
							$("#conte_mapa").css("top", "-1000px");
					}else if(data == "result"){
						$("#conte_mapa").css("top", "-1000px");
					}
				}
			},{
				lang:					$$.conf.lang,
				consumer_key:			$$.conf.consumer_key,
				oauth_token:			$$.conf.oauth_token,
				result_items_per_page:	'10',
				result_page:			'1',
				filter_name:			''
			});
		});
	}
};

function getPos (obj) {
	var output = new Object(), 
	mytop=0, myleft=0;
	while( obj) {
		mytop+= obj.offsetTop;
		myleft+= obj.offsetLeft;
		obj= obj.offsetParent;
	}
	output.left = myleft;
	output.top = mytop;
	return output;
}



/**
 * Control friends
 */
var citems = {
	paginar: undefined,
	paginar_cat: undefined,
	likes: undefined,
	
	ini: function(){
		citems.paginar = new pagination();
		citems.paginar_cat = new pagination();
		citems.likes = new clikes("div.ui_item", "#item_id", "#me_id", "user_like_items", "item_id");
		$(".ui_item .ui_like").live('click', citems.likes.events.barControl_click);
		
		//$(".add_friend, .remove_friend").live('click', cfriends.barControl_click);
		$("#ui_item_content #ui_pagination_itm .pag_items").live('click', citems.gotoPage_click);
		$("#ui_item_content #pagination_cat .pag_items").live('click', citems.gotoPageCat_click);
		$("#ui_item_content .item_subcat").live('click', citems.gotoCat_click);
		$("#ui_item_content .item_back").live('click', citems.gotoCatBack_click);
		
		//eventos actionbar
		$("#ui_item_content .itm_details").live('click', citems.openWinDetail_click);
		
		this.pagItemDetails();
	},
	
	gotoCatBack_click: function(){
		var item_back 	= $(this),
		pitems		= $(this).parents("div#ui_item_content"),
		category_id = "",
		pag			= '1',
		per_pag		= $("#per_pag", pitems).val(),
		
		//checar el arbol de las categorias
		tree = item_back.attr("cat_back"),
		pos  = tree.lastIndexOf(">");
		if(pos == -1){
			item_back.css("display", "none");
			item_back.attr("cat_back", "");
			category_id = tree.substr(pos+1);
		}else{
			item_back.attr("cat_back", tree.substr(0, pos));
			category_id = tree.substr(pos+1);
		}
		
		citems.getItems(pitems, category_id, pag, per_pag);
		citems.renderSubCats(pitems, category_id, pag);
		$("#category_id", pitems).val(category_id);
	},
	
	gotoCat_click: function(){
		var pitems		= $(this).parents("div#ui_item_content"),
		category_id = $(this).attr("cat_id"),
		pag			= '1',
		per_pag		= $("#per_pag", pitems).val();
		
		citems.getItems(pitems, category_id, pag, per_pag);
		citems.renderSubCats(pitems, category_id, pag);
		
		//arbol para el back de las categorias
		var item_back = $(".item_back", pitems);
		item_back.attr("cat_back", item_back.attr("cat_back")+
				(item_back.attr("cat_back") == ''? $("#category_id", pitems).val(): ">"+$("#category_id", pitems).val()));
		
		$("#category_id", pitems).val(category_id);
		$(".item_back", pitems).css("display", "block");
	},
	
	gotoPage_click: function(){
		var vthis		= this,
		pitems		= $(this).parents("div#ui_item_content"),
		category_id = $("#category_id", pitems).val(),
		pag			= $(this).attr("rel"),
		per_pag		= $("#per_pag", pitems).val();
		citems.getItems(pitems, category_id, pag, per_pag, vthis);
	},
	
	getItems: function(pitems, category_id, pag, per_pag, vthis){
		var me_id = $("#ui_item_content #tme_id").val();
		$$.item_query({
			lang:					$$.conf.lang,
			consumer_key:			$$.conf.consumer_key,
			oauth_token:			$$.conf.oauth_token,
			result_items_per_page:	per_pag,
			result_page:			pag,
			filter_category_id:		category_id,
			filter_user_id:			me_id,
			only_my_items:			'1',
			callback: function(data){
				citems.renderItems(data, pitems, vthis);
				citems.paginar.setVals({
					total_rows 		: data.total_rows,
					current_page	: pag,
					per_page		: per_pag,
					pag_class		: 'pag_items',
					obj_id			: "ui_pagination_itm"
				});
				citems.paginar.render();
			}
		});
	},
	
	renderItems: function(data, pitems, vthis){
		var leng = $(".ponaqui", pitems).length,
		remove	  	= (leng > 0)? "div.ponaqui": "div.ponaqui2",
		posi	  	= (leng > 0)? "div.ponaqui2": "div.ponaqui",
		num_cols 	= parseInt($("#num_cols", pitems).val()),
		cont_cols 	= 1,
		me_id 		= $("#tme_id", pitems).val(),
		like_sel	= '',
		unlike_sel	= '',
		detailed_item = $("#detailed_item", pitems).val(),
		open_detail_win_click = $("#open_detail_win_click", pitems).val(),
		
		class_cle = '',
		class_openwin = '';
		if(open_detail_win_click == '1')
			class_openwin = ' itm_details';
		
		var width_col = 165,
		img_wh = '70',
		class_det = ' mini';
		if(detailed_item == "1"){
			width_col = 310;
			
			img_wh = '45';
			class_det = '';
		}
		var html_actionbar = '',
		
		html = '<div class="'+posi.substr(4)+'" style="width:'+(width_col*num_cols)+'px;">';
		//for(var item = 0; item < data.items.length; item++){
		if(data.items.length > 0){
			for(var item in data.items){
				class_cle = ((item % num_cols) == 0)? ' clear': '';
				
				like_sel 	= '';
				unlike_sel	= '';
				if(data.items[item].like=="1")
					like_sel 	= ' ui_likes_sel';
				else if(data.items[item].like=="-1")
					unlike_sel 	= ' ui_likes_sel';
				
				if(detailed_item == '1'){
					html_actionbar = '<div class="ui_likes">'+
						'<a href="javascript:void(0);" class="ui_like like'+like_sel+'">'+lang.like+'</a>'+
						'<a href="javascript:void(0);" class="ui_like unlike'+unlike_sel+'">'+lang.unlike+'</a>'+
					'</div>'+
					'<ul class="action_bar">'+
						'<li id="action_bar_li-0" class="action_bar_li">'+
							'<a href="javascript:void(0);" class="action_bar itm_details">'+lang.details+'</a>'+
						'</li>'+
						(data.items[item].store.id!=undefined? '<li id="action_bar_li-1" class="action_bar_li">'+
							'<a href="javascript:void(0);" class="action_bar preview_store">'+lang.purchased_in+'</a>'+
						'</li>': '')+
						'<li id="action_bar_li-2" class="action_bar_li">'+
							'<a href="'+$$.lml_get_url("item", data.items[item].id)+'" class="action_bar">'+lang.comment+'</a>'+
						'</li>'+
					'</ul>';
				}
				
				html += '<div id="ui_item-'+item+'" class="ui_item'+class_det+class_cle+class_openwin+'">'+
						'<img src="'+(data.items[item].images.length>0? data.items[item].images[0].sizes[3].url: '')+'" class="img itm_details img_item'+class_det+'" width="'+img_wh+'" height="'+img_wh+'">'+
						'<div class="item_content">'+data.items[item].label+
						($$.is_array(data.items[item].brand)==45? 
								' <a href="javascript:void(0);" class="link_blue preview_brand" rel="'+
								data.items[item].brand.id+'">'+
								data.items[item].brand.name+'</a>': '')+'</div>'+
						'<div class="clear"></div>'+
						html_actionbar+
						'<div class="clear"></div>'+
						'<input type="hidden" id="item_id" value="'+data.items[item].id+'">'+
						'<input type="hidden" id="store_id" value="'+(data.items[item].store.id!=undefined? data.items[item].store.id:'')+'">'+
						'<input type="hidden" id="me_id" value="'+me_id+'">'+
					'</div>';
			}
		}else
			html += '<div class="error_msg">No hay items en esta categoría.</div>';
		html += '<div class="clear"></div>'+
		'</div>';
		
		var peffect = new effects();
		peffect.pagination({
			width_item	: width_col,
			num_cols	: num_cols,
			remove		: remove,
			pfriends	: $("div.items_content", pitems),
			vthis		: vthis,
			posi		: posi,
			html		: html
		});
		//citems.renderSubCats(data, pitems);
	},
	
	gotoPageCat_click: function(){
		var vthis		= this,
		pitems		= $(this).parents("div#ui_item_content"),
		category_id = $("#category_id", pitems).val(),
		pag			= $(this).attr("rel");
		citems.renderSubCats(pitems, category_id, pag, vthis);
	},
	
	renderSubCats: function(pitems, category_id, pag, vthis){
		var detailed_item = $("#detailed_item", pitems).val(),
		leng = $(".catponaqui", pitems).length,
		remove	  	= (leng > 0)? "div.catponaqui": "div.catponaqui2",
		posi	  	= (leng > 0)? "div.catponaqui2": "div.catponaqui",
		num_cols 	= parseInt($("#cat_num_cols", pitems).val()),
		per_pag 	= $("#cat_per_page", pitems).val(),
		
		width_col = 165;
		if(detailed_item == "1"){
			width_col = 310;
		}
		
		$$.category_query({
			lang:					$$.conf.lang,
			consumer_key:			$$.conf.consumer_key,
			oauth_token:			$$.conf.oauth_token,
			result_items_per_page:	per_pag,
			result_page:			pag,
			filter_category_parent:	category_id,
			callback: function(data){
				citems.paginar_cat.setVals({
					total_rows 		: data.total_rows,
					current_page	: pag,
					per_page		: per_pag,
					pag_class		: 'pag_items',
					obj_id			: "pagination_cat"
				});
				citems.paginar_cat.render();
			
				var html = '<div class="'+posi.substr(4)+'" style="width:'+(width_col*num_cols)+'px;">';
				//for(var item = 0; item < data.categories.length; item++){
				for(var item in data.categories){
					html += '<div class="item_subcat" cat_id="'+data.categories[item].id+'">'+
						data.categories[item].name+'</div>';
				}
				html += '<div class="clear"></div>'+
				'</div>';
				
				$("span.list_title", pitems).text(data.name);
				//$("div.item_subcat", pitems).remove();
				//$("div.items_subcats", pitems).append(html);
				
				var peffect = new effects();
				peffect.pagination({
					width_item	: width_col,
					num_cols	: num_cols,
					remove		: remove,
					pfriends	: $("div.items_subcats", pitems),
					vthis		: vthis,
					posi		: posi,
					html		: html
				});
			}
		});

	},
	
	openWinDetail_click: function(event){
		var vthis	= this, item_id, me_id, pcon_item;
		if($(event).attr("obj_type") == 'item'){
			item_id = $(event).attr("obj_id"),
			me_id = $(event).attr("me_id");
		}else{
			pcon_item = $(this).parents("div.ui_item");
			if(pcon_item.length == 0){
				item_id = $("#item_id", this).val(),
				me_id = $("#me_id", this).val();
			}else{
				item_id = $("#item_id", pcon_item).val(),
				me_id = $("#me_id", pcon_item).val();
			}
			event.stopPropagation();
		}
		
		$$.item_query({
			lang:					$$.conf.lang,
			consumer_key:			$$.conf.consumer_key,
			oauth_token:			$$.conf.oauth_token,
			result_items_per_page:	'20',
			result_page:			'1',
			filter_item_id:			item_id,
			return_comments:		'0',
			callback: function(data){
				if(data.status.code == 200){
					WindowsModalControlUtil.clearWindowsModalControl();
					
					var gallery = new ImageGalleryControl(), pos_like='',
					html_gall =  gallery.setVals({
						items		: data.items[0].images,
						width_item	: 180
					});
					
					var html_brand = (data.items[0].brand.length==undefined? 
						'<div class="itm_con_data"><strong>'+lang.brand+':</strong> <a href="javascript:void(0);" class="link_blue preview_brand close" rel="'+
						data.items[0].brand.id+'" close="item_detail" me_id="'+me_id+'">'+data.items[0].brand.name+'</a></div>': '');
					var html_price = (data.items[0].price? 
							'<div class="itm_con_data"><strong>'+lang.price+':</strong> '+data.items[0].price+'</div>': '');
					var html_store = (data.items[0].store.length==undefined? 
							'<div class="itm_con_data"><strong>'+lang.purchased_in+':</strong> <a href="javascript:void(0);" class="link_blue preview_store close" rel="'+
							data.items[0].store.id+'" close="item_detail" me_id="'+me_id+'">'+data.items[0].store.name+'</a></div>': '');
					
					var html_charac = '';
					//for(var item = 0; item < data.items[0].attributes.length; item++){
					for(var item in data.items[0].attributes){
						html_charac += '<div class="itm_con_subdata"><strong>'+
							data.items[0].attributes[item].name+':</strong> <span>&nbsp;'+
							data.items[0].attributes[item].value+'</span></div>';
					}
					
					var like_sel 	= '',
					unlike_sel	= '';
					if(data.items[0].like=="1")
						like_sel 	= ' ui_likes_sel';
					else if(data.items[0].like=="-1")
						unlike_sel 	= ' ui_likes_sel';
					if(data.items[0].images.length < 2)
						pos_like = ' style="top:0;"';
					
					var html = '<div id="item_detail">'+
					'	<div class="itm_detail_conte">'+
					'		<div class="itm_conte_left" style="width: 180px;">'+html_gall+
					'			<div class="clear"></div>'+
					'			<div class="ui_likes"'+pos_like+'>'+
					'				<a href="javascript:void(0);" class="ui_like like'+like_sel+'">'+lang.like+'</a>'+
					'				<a href="javascript:void(0);" class="ui_like unlike'+unlike_sel+'">'+lang.unlike+'</a>'+
					'			</div>'+
					'		</div>'+
					'		<div class="itm_conte_right" style="width: 305px;">'+
					'			<div class="itm_con_data"><strong>'+lang.title+':</strong> '+data.items[0].label+'</div>'+
								html_brand+
								html_price+
								html_store+
					'			<div class="itm_con_data"><strong>'+lang.features+'</strong>'+
					'				<div class="clear"></div>'+
									html_charac+
					'			</div>'+
					'		</div>'+
					'		<div class="clear"></div>'+
					'	</div>'+
					'	<div class="clear"></div>'+
					'	<input type="hidden" id="item_id" value="'+data.items[0].id+'">'+
					'	<input type="hidden" id="me_id" value="'+me_id+'">'+
					'	<a href="'+$$.lml_get_url("item", data.items[0].id)+'" class="link_blue lnk_see_all">'+lang.view_more+'</a>'+
					'</div>';
					$("body").append(html);
					$('img[id^="img_gallery"]', $("#item_detail")).load(gallery.events.barControl_click); //ajustar imagenes de galeria
					$(".pag_gallery", $("#item_detail")).click(gallery.events.barControl_click); //evento de las paginas de galeria
					
					var like_win = new clikes("div#item_detail", "#item_id", "#me_id", "user_like_items", "item_id");
					$("#item_detail .ui_like").live('click', like_win.events.barControl_click);
					
					//creamos el windows control
					var win = new WindowsModalControl();
					win.setVals({
						selector_obj: "#item_detail",
						title_win: data.items[0].label,
						width_win: 525,
						height_win: $("#item_detail").height()+75
					});
				}
			}
		});
		
	},
	
	pagItemDetails: function(){
		var num_items = $("#num_imgs_items").val(),
		
		gallery = new ImageGalleryControl(),
		html_gall =  gallery.setVals({
			num_items		: num_items,
			insert_in		: "#pitem_detail",
			ident			: 16,
			width_item		: 180
		}),
		like_win = new clikes("div#pitem_detail", "#item_id", "#me_id", "user_like_items", "item_id");
		$("#pitem_detail .ui_like").live('click', like_win.events.barControl_click);
	},
	
	
	asigAutocomplete: function(){
		var service, name_array='items', field_filter='filter_label', 
		field_par_name='label', json_data = {
			lang:					$$.conf.lang,
			consumer_key:			$$.conf.consumer_key,
			oauth_token:			$$.conf.oauth_token,
			result_items_per_page:	'10',
			result_page:			'1'
		};
		if($("#hid_itm_autocom").val() == '1'){ //items
			json_data['filter_label'] = '';
			service = "item/query.json";
		}else{ //amigos
			json_data['user_id'] = $("#me_id").val();
			json_data['filter_name'] = '';
			json_data['all'] = '1';
			service = 'user/friends.json';
			
			name_array='friends'; field_filter='filter_name'; field_par_name='full_name';
		}
		
		$('input[id^="items_aucomplete"]').each(function(){
			$(this).unbind('keydown');
			var auto = new autocomplete(this, {
				url_service: $$.conf.url_base_service+service,
				name_array: name_array,
				field_filter: field_filter,
				field_par_name: field_par_name,
				call_in_enter: true,
				callback: function(data){
					if(data == "sel_enter"){
						citems.addItemNewPostyle(this, $("#hid_itm_autocom").val()); //agrega el item a la lista del new postyle
					}else if(data == "no_result"){
						if($("#hid_itm_autocom").val() == '1')
							citems.showMiniFrmAddItem();
					}
				}
			}, json_data);
		});
	},
	addItemNewPostyle: function(objinput, type_autocom){
		if($("#imgitem_id_auto").val() != ""){
			var name_id = 'items_id', all_fields = '';
			if(type_autocom == '0'){
				name_id = 'users_id';
				all_fields = 'usr';
			}
				
			var html = '<span class="addedimg_items">'+$(objinput).val();
			 html += '<input type="hidden" class="add'+name_id+'" name="'+name_id+'[]" value="'+$("#imgitem_id_auto").val()+'">';
			 html += '<input type="hidden" class="addtag_'+all_fields+'description" name="tag_'+all_fields+'description[]" value="'+$(objinput).val()+'">';
			 html += '<input type="hidden" class="addtag_'+all_fields+'width" name="tag_'+all_fields+'width[]" value="'+$("#w").val()+'">';
			 html += '<input type="hidden" class="addtag_'+all_fields+'height" name="tag_'+all_fields+'height[]" value="'+$("#h").val()+'">';
			 html += '<input type="hidden" class="addtag_'+all_fields+'x" name="tag_'+all_fields+'x[]" value="'+$("#x1").val()+'">';
			 html += '<input type="hidden" class="addtag_'+all_fields+'y" name="tag_'+all_fields+'y[]" value="'+$("#y1").val()+'">';
			 html += '<span></span></span>';
			$("#items_added").append(html);
			$("#imgitem_id_auto").val("");
			$("#items_added .addedimg_items > span").click(citems.removeItemNewPostyle);
			var img = $("#add_img_postyle").imgAreaSelect({ instance: true });
			img.cancelSelection();
			$("#img_autocomplete").css("display", "none");
		}
	},
	removeItemNewPostyle: function(){
		$(this).parents(".addedimg_items").remove();
	},
	showMiniFrmAddItem: function(){
		var obj_au_item = $("#items_aucomplete11");
		$("div.ui_autocomplete", obj_au_item).remove();
		$("#conte_mapa").css("top", "-1000px");
		
		obj_au_item.append('<div class="ui_autocomplete" style="width:300px;border: 2px #000 solid;">'+
		'<form id="frmMinAddItems" onsubmit="return false;">'+
		'	<label>'+lang.brand+'</label> <input type="text" id="brand_aucomplete1" autocomplete="off"><br>'+ 
		'	<div id="brand_aucomplete11" class="auto_result">'+
		'		<input type="hidden" id="miniAddItem_brand" class="pst_ponid" name="brand_id">'+
		'	</div>'+
		'	<div id="miniItmDetal_show" class="yes"><label>'+lang.price+'</label> <input type="text" id="miniAddItem_price" size="10"> <br>'+
		'	<label>'+lang.purchased_in+'</label> <input type="text" id="store_aucomplete1" autocomplete="off"><br>'+
		'	<div id="store_aucomplete11" class="auto_result">'+
		'		<input type="hidden" id="miniAddItem_store" class="pst_ponid" name="store_id">'+
		'		<input type="hidden" id="miniAddItem_store_lat" class="pst_lat" name="store_lat">'+
		'		<input type="hidden" id="miniAddItem_store_lon" class="pst_lon" name="store_lon">'+
		'	</div></div>'+
		'	<div class="miniAddItmfoot">'+
		'		<a href="javascript:void(0);" class="link_blue miniItmDetal_togle">'+lang.more_detaild+'</a>'+
		'		<span id="preloader_item"></span>'+
		'		<input type="button" id="btn_publish_tag" name="save" value="'+lang.add+'" class="btn_publish"> <input type="submit" id="btn_pub_sub_tag" name="save" value="send" class="btn_publish">'+
		'	</div>'+
		'</form></div>');
		
		$("#frmMinAddItems").submit(citems.saveNewItem_submit);
		$("#frmMinAddItems .miniItmDetal_togle").toggle(function(){
			$("#miniItmDetal_show").show("slow");
			$(this).text(lang.hide_details);
			$("#miniItmDetal_show").removeClass("yes").addClass("no");
			$("#brand_aucomplete1").focus();
		}, function(){
			$("#miniItmDetal_show").hide("slow");
			$(this).text(lang.more_detaild);
			$("#miniItmDetal_show").removeClass("no").addClass("yes");
			$("#brand_aucomplete1").focus();
		});
		//$("#brand_aucomplete1").keydown(citems.textBox_keydown);
		$("#miniAddItem_price").keydown(citems.textBox_keydown);
		//$("#store_aucomplete1").keydown(citems.textBox_keydown);
		$("#btn_publish_tag").click(function(){
			$("#miniItmDetal_show").removeClass("no").addClass("yes");
			$("#btn_pub_sub_tag").click();
		});
		
		//$("#items_aucomplete1").focus();
		cBrand.asigAutocomplete();
		cStore.asigAutocomplete();
	},
	
	textBox_keydown: function(event){
		switch(event.which){
			case 13:
				var obj = $(this),
				obj_f = undefined; 
				if(obj.is("#brand_aucomplete1")){
					obj_f = $("#miniAddItem_price");
				}else if(obj.is("#miniAddItem_price")){
					obj_f = $("#store_aucomplete1");
				}
				
				if($("#miniItmDetal_show").is(".no")){
					if(obj.is("#store_aucomplete1")){
						//$("#btn_publish_tag").click();
					}else
						obj_f.focus();
				}
			break;
		}
	},
		
	saveNewItem_submit: function(){
		if($("#miniItmDetal_show").is(".yes")){
			var label = lml.addslashes($("#items_aucomplete1").val()),
			frm = $(this),
			brand_name = lml.addslashes($("#brand_aucomplete1", frm).val()),
			brand_id = $("#miniAddItem_brand", frm).val(),
			price = $("#miniAddItem_price", frm).val(),
			
			store_id = $("#miniAddItem_store", frm).val(),
			store_label = lml.addslashes($("#store_aucomplete1", frm).val()),
			store_lat = $("#miniAddItem_store_lat", frm).val(),
			store_lon = $("#miniAddItem_store_lon", frm).val(),
			
			json_para = '';
			if(brand_id != '')
				json_para += brand_id!=''? '"brand_id":"'+brand_id+'",': '';
			else
				json_para += brand_name!=''? '"brand_name":"'+brand_name+'",': '';
			
			if(store_id != '')
				json_para += '"bought_in":"'+store_id+'",';
			else if(store_label!='' && store_lat!='' && store_lon!=''){
				json_para += '"bought_label":"'+store_label+'",'+
				'"bought_lat":"'+store_lat+'",'+
				'"bought_lon":"'+store_lon+'",';
			}
			
			json_para += price!=''? '"price":"'+price+'",': '';
			
			$("#preloader_item").html('<img src="'+$$.conf.url_base+'application/images/loader_min.gif" width="16" height="16">');
			eval('$$.item_add({'+
			'	lang:				$$.conf.lang,'+
			'	consumer_key:		$$.conf.consumer_key,'+
			'	oauth_token:		$$.conf.oauth_token,'+
			'	cat_attr_id:		["1"],'+
			'	value:				["2"],'+
			'	label:				"'+label+'",'+json_para+
			'	callback: citems.saveNewItemResponse'+
			'});');
		}
		
		return false;
	},
	saveNewItemResponse: function(data){
		var preload = $("#preloader_item");
		preload.html("");
		if(data.status.code == 200){
			$("#imgitem_id_auto").val(data.item_id);
			$("div.ui_autocomplete").remove();
			$("#conte_mapa").css("top", "-1000px");
			//$("#items_aucomplete1").focus();
			citems.addItemNewPostyle($("#items_aucomplete1"));
		}else{
			preload.html("<span>"+data.status.message+"</span>");
		}
	}
};




/**
 * Control friends
 */
var cpostyles = {
	paginar: undefined,
	likes: undefined,
	
	ini: function(){
		cpostyles.likes = new clikes("div.postyle", "#postyle_id", "#me_id", 
				"user_like_postyle", "postyle_id");
		$("div.postyle > div.ui_likes > .ui_like").live('click', cpostyles.likes.events.barControl_click);
		$(".postyle_delete").live('click', cpostyles.barPostyleDelete_click);
		
		//$(".postyle_more_result").live('click', cpostyles.gotoPage_click);
		$(".postyle_more_result").click(cpostyles.gotoPage_click);
		
		//postyle detaild
		$("#postyle_conte_detail .tag_imgs").hover(
			cpostyles.tagPostyle_hover,
			cpostyles.tagPostyle_hover
		);
		$("#postyle_conte_detail .mini_item").hover(
			cpostyles.tagMiniPostyle_hover,
			cpostyles.tagMiniPostyle_hover
		);
		$("#postyle_conte_detail .mini_usritem").hover(
			cpostyles.tagMiniPostyle_hover,
			cpostyles.tagMiniPostyle_hover
		);
		
		//new postyle
		$(".new_postyle_clear").click(cpostyles.newPostyleClear);
		$("#frmSavePostyle").submit(cpostyles.saveNewPostyle_submit);
	},
	
	tagMiniPostyle_hover: function(event){
		var vthis = $(this), attr_name = (vthis.attr("id_item")==undefined? "id_user": "id_item");
		if(event.type == "mouseenter"){
			$("#tag_imgs"+vthis.attr(attr_name)).removeClass("no_border");
		}else{
			$("#tag_imgs"+vthis.attr(attr_name)).addClass("no_border");
		}			
	},
	
	tagPostyle_hover: function(event){
		var vthis = $(this);
		if(event.type == "mouseenter"){
			vthis.removeClass("no_border");
			$("span", vthis).css("display", "block");
			$("span", vthis).css("left", parseInt((vthis.width()-$("span", vthis).width())/2)+"px");
		}else{
			vthis.addClass("no_border");
			$("span", vthis).css("display", "none");
		}			
	},
	
	barPostyleDelete_click: function(){
		var ppostyle	= $(this).parents("div.postyle"),
		postyle_id	= $("#postyle_id", ppostyle).val();
		
		$$.postyle_disable({
			lang:				$$.conf.lang,
			consumer_key:		$$.conf.consumer_key,
			oauth_token:		$$.conf.oauth_token,
			postyle_id:			postyle_id,
			enable:				'0',
			callback: function(data){
				$(ppostyle).fadeOut(500, function(){
					$(ppostyle).remove();
				});
				//alert(data.status.message);
			}
		});
	},
	
	gotoPage_click: function(){
		var vthis		= this,
		objclck		= $(this),
		pposty		= $(objclck.attr("insert")),
		per_pag			= $("#per_page", pposty).val(),
		filter_user_id	= $("#filter_user_id", pposty).val(),
		filter_order	= $("#filter_order", pposty).val(),
		filter_both		= $("#filter_both", pposty).val(),
		pag				= objclck.attr("page"),
		btn_parent		= $(objclck).parents('.btnsee_more');
		
		objclck.css("display", 'none');
		btn_parent.append('<img src="'+$$.conf.url_base+'application/images/loader_min.gif" id="loader_more_postyle" width="16" height="16">');
		$$.postyle_query({
			lang:					$$.conf.lang,
			consumer_key:			$$.conf.consumer_key,
			oauth_token:			$$.conf.oauth_token,
			result_items_per_page:	per_pag,
			result_page:			pag,
			filter_user_id:			filter_user_id,
			filter_order:			filter_order,
			filter_both:			filter_both,
			callback: function(data){
				if(data.status.code == 200){
					cpostyles.renderItems(data, pposty);
					objclck.attr("page", parseInt(pag)+1);
					
					if((pag*per_pag) >= data.total_rows) //ya no hay mas resultados
						objclck.remove();
					else
						objclck.css("display", 'block');
					$("#loader_more_postyle").remove();
				}
			}
		});
	},
	
	renderItems: function(data, pposty){
		var html = '',
		like_sel 	= '',
		unlike_sel	= '',
		me_id = $("#me_id", pposty).val(),
		$width_img = 200,
		$width = $width_img,
		$height = $width_img,
		$style = '',
		$porcent;
		
		for(var item in data.postyles){
			html += '<div id="ui_postyle-'+item+'" class="postyle">';
			
			
			html += '<div class="postyle_left">'+
					'<span>'+(data.postyles[item].images[0]? 
						'<a href="'+$$.lml_get_url("postyle", data.postyles[item].id)+'&lang='+$$.conf.lang+'">'+
						'<img src="'+data.postyles[item].images[0]["sizes"][1]["url"]+'" class="img img_postyle"></a>': '')+
					'</span>'+
				'</div>';
			
			html += '<div class="postyle_content">'+
				'<div class="postyle_descrip">'+
					(data.postyles[item].user_images[0]? 
						'<img src="'+data.postyles[item].user_images[0]["sizes"][3]["url"]+'" class="img" width="30" height="30">': '')+
					'<span>'+
						'<a href="'+$$.lml_get_url("user", data.postyles[item].user_id)+'" class="link_blue lpost_usr">'+
						data.postyles[item].user_name+" "+data.postyles[item].user_last_name+'</a>:'+ 
						data.postyles[item].description+
					'</span>'+
					'<div class="clear"></div>'+
				'</div>'+
				
				'<div class="postyle_ocati_date">'+
					'<span>'+lang.ocation+':</span> '+data.postyles[item].ocation_name+' | '+
					'<span>'+lang.date+':</span> '+data.postyles[item].date_added+
				'</div>';
				
				//Colocamos los items del postyle con el control mini_items
				html += cMiniItems.renderMiniItems(data.postyles[item].tags, me_id);
				
			html += '</div>'+
			'<div class="clear"></div>';
			//like del postyle
			like_sel 	= '';
			unlike_sel	= '';
			if(data.postyles[item].i_like=="1")
				like_sel 	= ' ui_likes_sel';
			else if(data.postyles[item].i_like=="-1")
				unlike_sel 	= ' ui_likes_sel';
			html += '<div class="ui_likes">'+
				'<a href="javascript:void(0);" class="ui_like'+like_sel+' like">'+lang.like+'</a>'+
				'<a href="javascript:void(0);" class="ui_like'+unlike_sel+' unlike">'+lang.unlike+'</a>'+
			'</div>';
			
			html += '<div class="clear"></div>';
			
			//comentarios del postyle			
			html += '<div class="comments" style="width:590px;">';
			//html += ccomments.generateComment(data, null, true);
			html += '<div class="clear" style="height:4px;"></div>'+
				'<div class="bar_comment">'+
				'<ul class="action_bar">'+
					'<li id="action_bar_li-0" class="action_bar_li">'+
						'<a href="javascript:void(0);" class="action_bar rankear">'+lang.rankear+'</a>'+
					'</li>';
					if(data.postyles[item].can_be_deleted == '1'){
						html += '<li id="action_bar_li-1" class="action_bar_li">'+
						'<a href="javascript:void(0);" class="action_bar postyle_delete">'+lang.deletee+'</a>'+
					'</li>';
					}
					html += '<li id="action_bar_li-2" class="action_bar_li no_border">'+
						'<a href="javascript:void(0);" class="action_bar acomment">'+lang.comment+'</a>'+
					'</li>';
					html += '<li id="action_bar_li-3" class="action_bar_li no_show">'+
						'<a href="javascript:void(0);" class="action_bar link_all_comment link_ver_mas">'+lang.see_all+'</a>'+
					'</li>'+
				'</ul>';
				
				html += '<div class="clear"></div>'+
				'</div>';
				
			html += '<form id=frm_comment class="frm_comment" style="display: none;" onsubmit="return false;">'+
				'<span class="title">'+lang.add_comment+'</span>'+
				'<div class="add_comment">'+
					'<img src="" width="40" height="40" class="img">'+
					'<div class="comm_conte" style="width:442px;">'+
						'<textarea name="txt_comment" class="txt_comment expand50-200" rows="3" cols="60"></textarea>'+
						'<span class="comm_conte_pic"></span>'+
					'</div>'+
					'<div class="clear"></div>'+
				'</div>'+
				'<input type="submit" name="sub_comment" value="'+lang.comment+'" class="sub_comment">'+
				'<div class="clear"></div>'+
			'</form>';
			
			html += '<div class="clear"></div>'+
				'<input type="hidden" id="action_method" value="postyle">'+
				'<input type="hidden" id="content_parent" value="div.postyle">'+
				'<input type="hidden" id="id_obj_comment" value="postyle_id">'+
			'</div>';
			
			html += '<input type="hidden" id="postyle_id" value="'+data.postyles[item].id+'">'+
			'<input type="hidden" id="postyle_user_id" value="'+data.postyles[item].user_id+'">'+
		'</div>';
		}
		
		pposty.append(html);
		$(".postyle_left .img.img_postyle").load(cpostyles.loadImgRender);
		$(".expand50-200").TextAreaExpander();
	},
	loadImgRender: function(event){
		var $height=200, $width=200, $width_img=200,
		target = getTarget(event), $porcent;
		
		try{
			$width = target.width;
			$height = target.height;
		}catch(e){
			$height=200;
			$width=200;
		}
		
		$porcent	= $width_img*100/$width;
		$width		= $width_img;
		$height		= parseInt($height*$porcent/100);
		
		if($height > 400){
			$porcent	= 400*100/$height;
			$height		= 400;
			$width		= parseInt($width_img*$porcent/100);
			
			$(this).css("margin-left", parseInt(($width_img-$width)/2)+"px");
		}		
		$(this).attr("width", $width).attr("height", $height);
	},
	
	
	
	newPostyleClear: function(){
		var ob_form = $(".frm_postyle_publish");
		var img = $("#add_img_postyle").imgAreaSelect({ instance: true });
		img.cancelSelection();
		$("input[type='hidden'], input[type='text'], textarea", ob_form).val("");
		$(".addedimg_items", ob_form).remove();
		$(".img_view_tag").css("display", "none");
		$(".ifr_img_postyle").css("display", "block");
		$("#add_img_postyle").unbind("click", lml.clickSelect);
	},
	saveNewPostyle_submit: function(){
		var frm = $(this),
		image_url = $("#img_upload_file", frm).val(),
		description = lml.addslashes($("textarea", frm).val()),
		ocation_id = $("#autocation_id", frm).val(),
		ocation_label = lml.addslashes($("#ocation_aucomplete1", frm).val()),
		items_id = new Array(), tag_description = new Array(), tag_width = new Array(),
		tag_height = new Array(), tag_x = new Array(), tag_y = new Array(),
		users_id = new Array(), tag_usrdescription = new Array(), tag_usrwidth = new Array(),
		tag_usrheight = new Array(), tag_usrx = new Array(), tag_usry = new Array();
		
		//obtengo las tags de items
		$(".additems_id").each(function(index){
			var conpar = $(this).parents(".addedimg_items");
			items_id[index] 	= $(this).val();
			tag_description[index] = $(".addtag_description", conpar).val();
			tag_width[index] 	= $(".addtag_width", conpar).val();
			tag_height[index] 	= $(".addtag_height", conpar).val();
			tag_x[index] 		= $(".addtag_x", conpar).val();
			tag_y[index] 		= $(".addtag_y", conpar).val();
		});
		//obtengo las tags de usaurios
		$(".addusers_id").each(function(index){
			var conpar = $(this).parents(".addedimg_items");
			users_id[index] 	= $(this).val();
			tag_usrdescription[index] = $(".addtag_usrdescription", conpar).val();
			tag_usrwidth[index] 	= $(".addtag_usrwidth", conpar).val();
			tag_usrheight[index] 	= $(".addtag_usrheight", conpar).val();
			tag_usrx[index] 		= $(".addtag_usrx", conpar).val();
			tag_usry[index] 		= $(".addtag_usry", conpar).val();
		});
		
		var json_para = '';
		json_para += description!=''? '	"description":"'+description+'",': '';
		if(items_id.length > 0){ //se agregan los tags de items
			json_para += '	"items_id": ["'+items_id.join('","')+'"],';
			json_para += '	"tag_description": ["'+tag_description.join('","')+'"],';
			json_para += '	"tag_width": ["'+tag_width.join('","')+'"],';
			json_para += '	"tag_height": ["'+tag_height.join('","')+'"],';
			json_para += '	"tag_x": ["'+tag_x.join('","')+'"],';
			json_para += '	"tag_y": ["'+tag_y.join('","')+'"],';
		}
		
		if(users_id.length > 0){ //se agregan los tags de usuarios
			json_para += '	"users_id": ["'+users_id.join('","')+'"],';
			json_para += '	"tag_usrdescription": ["'+tag_usrdescription.join('","')+'"],';
			json_para += '	"tag_usrwidth": ["'+tag_usrwidth.join('","')+'"],';
			json_para += '	"tag_usrheight": ["'+tag_usrheight.join('","')+'"],';
			json_para += '	"tag_usrx": ["'+tag_usrx.join('","')+'"],';
			json_para += '	"tag_usry": ["'+tag_usry.join('","')+'"],';
		}
		
		if(ocation_id!='')
			json_para += '	"ocation_id":"'+ocation_id+'",';
		else if(ocation_label!='')
			json_para += '	"ocation_label":"'+ocation_label+'",';
		
		$("#preloader_postyle").html('<img src="'+$$.conf.url_base+'application/images/loader_min.gif" width="16" height="16">');
		eval('$$.postyle_add({'+
		'	lang:				$$.conf.lang,'+
		'	consumer_key:		$$.conf.consumer_key,'+
		'	oauth_token:		$$.conf.oauth_token,'+
		'	image_url:			"'+image_url+'",'+json_para+
		'	callback: cpostyles.saveNewPostyleResponse'+
		'});');
		
		return false;
	},
	saveNewPostyleResponse: function(data){
		var preload = $("#preloader_postyle");
		preload.html("");
		if(data.status.code == "200"){
			window.location = $$.conf.url_base+"?lang="+$$.conf.lang;
		}else{
			$("#preloader_postyle").html("<span>"+data.status.message+"</span>");
		}
	}
};





/**
 * Control Ocation
 */
var cOcation = {
	ini: function(){
		//if($('input[id^="ocation_aucomplete"]').length>0)
		cOcation.asigAutocomplete();
	},
	
	asigAutocomplete: function(){
		$('input[id^="ocation_aucomplete"]').each(function(){
			var auto = new autocomplete(this, {
				url_service: $$.conf.url_base_service+"ocation/query.json",
				name_array: "ocations",
				field_filter: "filter_name",
				callback: function(data){
				
				}
			},{
				lang:					$$.conf.lang,
				consumer_key:			$$.conf.consumer_key,
				oauth_token:			$$.conf.oauth_token,
				result_items_per_page:	'10',
				result_page:			'1',
				filter_name:			''
			});
		});
	}
};



/**
 * Autocompletar
 */
function autocomplete(obj, conf, params){
	this.params = params;
	var vthis = this;
	
	this.proto = {
		len_search: 2, //numero minimi de caracteres para que se lanse la busqueda
		searching: undefined, //timer para realizar los filtros de busqueda
		delay: 300, //tiempo de espera para realizar las busquedas
		term: "", //es el creterio por el que se filtraran los datos en la busqueda
		url_service: "",
		name_array: "ocations",
		field_filter: "filter_name",
		field_par_name: "name",
		obj_tab: "",
		call_in_enter: false,
		call_enter_all: false,
		call_on_result: false,
		hide_in_select: true,
		blok: true,
		callback: undefined,
		obj_intput: undefined,
		config: function(){
			$(obj).keydown(this.evenKeyDownBuscar);
		},
		evenKeyDownBuscar: function(event){
			vthis.proto.obj_intput = this;
			var obj_list = $("#"+$(this).attr("id")+"1 .ui_autocomplete");
			
			switch(event.which){
				case 13:
					var seless = $("li.selec", obj_list);
					seless.click();
					if(vthis.proto.call_enter_all && seless.length == 0)
						vthis.proto.callback.call(this, "sel_enter");
					
					event.preventDefault();
				break;
				case 9:
					if(vthis.proto.obj_tab != ""){
						$(vthis.proto.obj_tab).focus();
					}
				break;
				case 40:
					vthis.proto._moveTo(obj_list, "down");
				break;
				case 38:
					vthis.proto._moveTo(obj_list, "up");
				break;
				default:
					var objtarget = this;
					obj_list.remove();
					//$("#conte_mapa").css("top", "-1000px");
					//se lanza un timer para realizar la busqueda ya que el keydown no tiene el caracter
					//precionado
					clearTimeout(vthis.proto.searching);
					vthis.proto.searching = setTimeout(function() {
						var term_aux = $(objtarget).val();
						if(vthis.proto.term.length >= term_aux.length){
							vthis.proto.blok = true;
						}
						
						if(term_aux.length > vthis.proto.len_search && term_aux != vthis.proto.term && 
								vthis.proto.blok){
							vthis.proto.term = term_aux;
							vthis.params[vthis.proto.field_filter] = term_aux;
							$.ajax({
								type: 		$$.conf.method,
								url: 		vthis.proto.url_service,
								data: 		(vthis.params),
								dataType:	$$.conf.format,
								success: 	function(res){
									if(res.status.code == "200"){
										if(res[vthis.proto.name_array].length > 0){
											vthis.proto.renderItems(res[vthis.proto.name_array]);
											if(vthis.proto.call_on_result)
												vthis.proto.callback.call(this, "result");
										}else{
											vthis.proto.callback.call(this, "no_result");
											vthis.proto.blok = false;
										}
									}
								},
								error: function(jqXHR, textStatus, errorThrown){
									alert(textStatus);
								}
							});
						}else
							vthis.proto.callback.call(this, "no_result");
					}, vthis.proto.delay);
				break;
			}
		},
		renderItems: function(source){
			var objj = $(vthis.proto.obj_intput), data_ext='',
			
			stritems = '<ul class="ui_autocomplete" style="top:0px; left:'+objj.position().left+'px; width:'+objj.width()+'px;">';
			for(var item in source){
				data_ext = '';
				if(vthis.proto.name_array == 'items')
					data_ext = (source[item]['brand'].name!=undefined)? 
							(source[item]['brand'].name!=''? " "+source[item]['brand'].name:''): '';
				stritems += '<li class="auto_item" id="'+source[item].id+'">'+source[item][vthis.proto.field_par_name]+data_ext+'</li>';
			}
			stritems += '</ul>';
			var obj_res = $("#"+objj.attr("id")+"1");
			$("ul.ui_autocomplete", obj_res).remove();
			obj_res.append(stritems).css("display", "block");
			$(".pst_ponid", obj_res).val("");
			$("ul.ui_autocomplete .auto_item").click(vthis.proto.selectItem);
		},
		selectItem: function(){
			var paren = $("#"+$(vthis.proto.obj_intput).attr("id")+"1"); //$(this).parents(".auto_result");
			$(".pst_ponid", paren).val($(this).attr("id"));
			$(vthis.proto.obj_intput).val($(this).text());
			
			if(vthis.proto.hide_in_select){
				$("ul.ui_autocomplete", paren).remove();
			}
			
			if(vthis.proto.call_in_enter)
				vthis.proto.callback.call(vthis.proto.obj_intput, "sel_enter");
		},
		_moveTo: function(obj_list, direction){
			var numli = 0,
			index_sel = 0;
			$("li", obj_list).each(function(index){
				if($(this).is(".selec")){
					index_sel = (direction=="down"? index+2: index);
				}
				numli++;
			});
			if(direction == "down"){
				index_sel = index_sel==0? 1: index_sel;
				if(index_sel <= numli){
					$("li", obj_list).removeClass("selec");
					$("li:nth-child("+index_sel+")", obj_list).addClass("selec");
				}
			}else{
				index_sel = index_sel==0? 0: index_sel;
				if(index_sel >= 1){
					$("li", obj_list).removeClass("selec");
					$("li:nth-child("+index_sel+")", obj_list).addClass("selec");
				}
			}
		}
	};
	this.setConf(conf);
	this.proto.config();
	
}
autocomplete.prototype.setConf = function(conf){
	if(conf!=undefined){
		try{
			for(var item in conf){
				this.proto[item] = conf[item];
			}
		}catch(e){}
	}
};




/**
 * Control friends
 */
var cMiniItems = {
	likes: undefined,
	timeount: undefined,
	
	ini: function(){
		cMiniItems.likes = new clikes("div.mini_item", "#item_id", "#me_id", 
				"user_like_items", "item_id", ".mini_item");
		$(".mini_item .ui_like").live('click', cMiniItems.likes.events.barControl_click);
		
		$(".ui_mini_items .mini_item").live({
			mouseenter: cMiniItems.miniItem_hover,
			mouseleave: cMiniItems.miniItem_hover
		});
		
		$(".ui_mini_items .mini_item .imini_info").live('click', function(event){
			citems.openWinDetail_click(this);
		});
	},
	
	miniItem_hover: function(event){
		var obj = this,
		paren = $(this).parents(".ui_mini_items");
		
		if(event.type == "mouseenter"){
			clearTimeout(cMiniItems.timeount);
			cMiniItems.timeount = setTimeout(function(){
				paren.css("height", paren.height()+"px");
				var mini_itemsr = $(".mini_item", paren);
				for(var i=mini_itemsr.length-1; i >= 0; i--){
					$(mini_itemsr[i]).css({
						position: 	"absolute",
						top: 		($(mini_itemsr[i]).offset().top-8)+"px",
						left:		$(mini_itemsr[i]).offset().left+"px"
					});
					$(mini_itemsr[i]).css("z-index", "90");
				}
				$(obj).css("border", "1px #c1c1c1 solid");
				$(obj).css("z-index", "100");
				$(".imini_like", obj).show("slow");
			}, 600);
		}else{
			clearTimeout(cMiniItems.timeount);
			$(".imini_like", this).hide(200, function(){
				var mini_itemsr = $(".mini_item", paren);
				for(var i=0; i < mini_itemsr.length; i++){
					$(mini_itemsr[i]).css({
						position: 	"inherit",
						border:		"none"
					});
				}
			});
		}
	},
	
	renderMiniItems: function(items, me_id){
		var html = '',
		like_sel 	= '', unlike_sel	= '',
		html_info	= '', html_like	= '', html_inputs	= '', attr_id = '', class_tag = '',
		
		$item_mcar = 15,
		$brand_mcar = 30,
		$type_control = 1;//1:items, 2:users
		
		html += '<div class="ui_mini_items">';
		
		for(var item in items){
			$type_control = items[item].item_label==undefined? 2: 1;
			
			items[item].item_label = items[item].item_label!=null && 
					items[item].item_label!=undefined? items[item].item_label: items[item].user_name;
			items[item].brand_name = items[item].brand_name!=null? items[item].brand_name: '';
			
			var $len_itm = items[item].item_label.length,
			$len_bran = items[item].brand_name.length,
			
			$title_brand = '',
			$title_item = '',
			$txt_brand = '',
			$txt_item = '';
			if($len_bran+$len_itm > $item_mcar+$brand_mcar){
				if($len_bran > $brand_mcar){
					$txt_brand = items[item].brand_name.substr(0, $brand_mcar-3)+'...';
					$title_brand = ' title="'+items[item].brand_name+'"';
				}
				if($len_itm > $item_mcar){
					$txt_item = items[item].item_label.substr(0, $item_mcar-3)+'...';
					$title_item = ' title="'+items[item].item_label+'"';
				}
			}else{
				$txt_brand = items[item].brand_name;
				$txt_item = items[item].item_label;
			}
			
			//Creamos el html
			like_sel = '', unlike_sel = '', html_info = '', html_like = '', html_inputs = '', attr_id = '';
			if($type_control == 1){ //tipo items
				html_info = '<span class="item_label"'+$title_item+'>'+$txt_item+'</span> '+
				'<a href="javascript:void(0);" class="link_blue preview_brand"'+$title_brand+' rel="'+items[item].brand_id+'" me_id="'+items[item].user_id+'">'+$txt_brand+'</a> '+
				'<span class="num_likes">'+items[item].nums_likes+'</span>';
				
				html_like += '<div class="imini_like">';
				if(items[item].like=="1")
					like_sel 	= ' ui_likes_sel';
				else if(items[item].like=="-1")
					unlike_sel 	= ' ui_likes_sel';
				html_like += '<div class="imini_like"><div class="ui_likes">'+
								'<a href="javascript:void(0);" class="ui_like'+like_sel+' like">'+lang.like+'</a>'+
								'<a href="javascript:void(0);" class="ui_like'+unlike_sel+' unlike">'+lang.unlike+'</a>'+
							'</div><div class="clear"></div>'+
						'</div>'+
						'<div class="clear"></div>';
				
				html_inputs = '<input type="hidden" id="item_id" value="'+items[item].item_id+'">';
				attr_id = 'id_item="'+items[item].item_id+'"';
				class_tag = 'mini_item';
			}else{ //tipo usuarios
				if(items[item].user_status == '1')
					html_info = '<a href="'+$$.lml_get_url('user', items[item].user_id)+'" class="link_blue"'+$title_item+'>'+$txt_item+'</a> ';
				else
					html_info = '<span class="item_label"'+$title_item+'>'+$txt_item+'</span> ';
				html_inputs = '<input type="hidden" id="user_id" value="'+items[item].user_id+'">';
				attr_id = 'id_user="'+items[item].user_id+'"';
				class_tag = 'mini_usritem';
			}
			
			html += '<div class="'+class_tag+'" '+attr_id+'>'+
				'<div class="imini_info">'+
					html_info+
				'</div>';
			html += html_like;
			html += 
				'</div>'+
				html_inputs+
				'<input type="hidden" id="me_id" value="'+me_id+'">'+
			'</div>';
		}
		html += '	<div class="clear"></div>'+
		'</div>';
		
		return html;
	}
};




/**
 * Control friends
 */
var cfriends = {
	paginar: undefined,
	
	ini: function(){
		cfriends.paginar = new pagination();
		$(".add_friend, .remove_friend").live('click', cfriends.barControl_click);
		$(".pag_friends").live('click', cfriends.gotoPage_click);
	},
	
	barControl_click: function(){
		var vthis 		= this,
		friend	 	= $(this).parents("div.ui_friends"),
		user_id 	= $("#user_id", friend).val(),
		me_id 		= $("#me_id", $(this).parents("div#ui_friends_content")).val();
		me_id = me_id==undefined? $("#me_id", friend).val(): me_id;
		
		($(this).is(".add_friend")? cfriends.add_friend(me_id, user_id, vthis): 
			cfriends.remove_friend(me_id, user_id, friend, vthis));		
	},
	
	add_friend: function(user_id, friend_id, vthis){
		$$.user_friend_invite({
			lang:			$$.conf.lang,
			consumer_key:	$$.conf.consumer_key,
			oauth_token:	$$.conf.oauth_token,
			user_id:		user_id,
			friend_id:		friend_id,
			action:			FriendAction.INVITE,
			callback: function(data){
				if(data.status.code == 200){
					$(vthis).text(lang.cancel_invitation).removeClass("add_friend").addClass("remove_friend request");
				}
				alert(data.status.message);
			}
		});
	},
	
	remove_friend: function(user_id, friend_id, friend, vthis){
		$$.user_friend_remove({
			lang:			$$.conf.lang,
			consumer_key:	$$.conf.consumer_key,
			oauth_token:	$$.conf.oauth_token,
			user_id:		user_id,
			friend_id:		friend_id,
			callback: function(data){
				if(data.status.code == 200){
					if($(vthis).is(".request"))
						$(vthis).text(lang.add).removeClass("remove_friend request").addClass("add_friend");
					else
						friend.remove();
				}
				alert(data.status.message);
			}
		});
	},
	
	gotoPage_click: function(){
		var vthis 		= this,
		pfriends	= $(this).parents("div#ui_friends_content"),
		pContefriend= $("div.friends_content", pfriends),
		user_id 	= $("#list_user_id", pfriends).val(),
		pag			= $(this).attr("rel"),
		per_pag		= $("#per_page", pfriends).val();
		
		$$.user_friends({
			lang:					$$.conf.lang,
			consumer_key:			$$.conf.consumer_key,
			oauth_token:			$$.conf.oauth_token,
			result_items_per_page:	per_pag,
			result_page:			pag,
			user_id:				user_id,
			callback: function(data){
				cfriends.renderItems(data, pContefriend, vthis, pfriends);
				cfriends.paginar.setVals({
					total_rows 		: data.total_rows,
					current_page	: pag,
					per_page		: per_pag,
					pag_class		: 'pag_friends'
				});
				cfriends.paginar.render();
			}
		});
	},
	
	renderItems: function(data, pfriends, vthis, pfriends2){
		var leng = $(".ponaqui", pfriends).length,
		remove	  = (leng > 0)? "div.ponaqui": "div.ponaqui2",
		posi	  = (leng > 0)? "div.ponaqui2": "div.ponaqui",
		view_actionbar = $("#view_actionbar", pfriends2).val(),
		num_cols = parseInt($("#nums_cols", pfriends2).val()),
		width_control = parseInt($("#width_control", pfriends2).val()),
		cont_cols = 1,
		
		html = '<div class="'+posi.substr(4)+'" style="width:'+((width_control-10)*num_cols)+'px;">';
		
		//for(var item = 0; item < data.friends.length; item++){
		for(var item in data.friends){
			var bar_friend_class = "add_friend",
			bar_friend = lang.add_friends,
			barcontrol = '',
			last_postyle = '',
			class_cols = '',
			height_items = '';
			
			if(view_actionbar == "1"){
				if(data.friends[item].is_my_friend == "1"){
					bar_friend_class = "remove_friend";
					bar_friend = lang.remove_friends;
					if(data.friends[item].last_postyle != '')
						last_postyle = '<li id="action_bar_li-'+item+'" class="action_bar_li">'+
						'<a href="'+$$.lml_get_url('postyle', data.friends[item].last_postyle)+'" class="action_bar see_last_postyle">'+lang.see_last_postyle+'</a>'+
					'</li>';
				}
				barcontrol += '<ul class="action_bar">'+
					'<li id="action_bar_li-'+item+'" class="action_bar_li">'+
					'<a href="javascript:void(0);" class="action_bar '+bar_friend_class+'">'+bar_friend+'</a>'+
				'</li>'+last_postyle+'</ul>';
			}else
				height_items = 'height:50px;';
			
			class_cols = '';
			if(cont_cols < num_cols || cont_cols==1)
				class_cols = cont_cols==1? ' fborder'+(num_cols==1?' nob':''):' border';
			if(cont_cols == num_cols)
				cont_cols = 0;
			cont_cols++;
			
			html += '<div id="ui_friends-'+item+'" class="ui_friends'+class_cols+'" style="width:'+(width_control-20)+'px;'+height_items+'">'+
					'<img src="'+(data.friends[item].images.length>0? data.friends[item].images[3].file_name: '')+'" class="img img_friend" width="35" height="35">'+
					'<div class="user_content" style="width:'+(width_control-66)+'px;">'+
						'<a href="'+$$.lml_get_url('user', data.friends[item].id)+'" class="link_blue">'+data.friends[item].name+" "+data.friends[item].last_name+'</a></div>'+
					'<div class="clear"></div>'+
					barcontrol+
					'<input type="hidden" id="user_id" value="'+data.friends[item].id+'">'+
					'<input type="hidden" id="last_postyle" value="'+data.friends[item].last_postyle+'">'+
					'<div class="clear"></div>'+
				'</div>';
		}
		html += '<div class="clear"></div>'+
			'</div>';
		
		var peffect = new effects();
		peffect.pagination({
			width_item	: (width_control-10),
			num_cols	: num_cols,
			remove		: remove,
			pfriends	: pfriends,
			vthis		: vthis,
			posi		: posi,
			html		: html
		});
		
	}
};


var cTabControl = {
	ini: function(){
		$(".ui_tab_control .plink").live("click", cTabControl.changeTab_click);
		$(".ui_tab_control .clink").live("click", cTabControl.changeSubTab_click);
	},
	
	changeTab_click: function(){
		var ptab 	= $(this).parents(".ui_tab_control"),
		subtab 	= $("#tchild"+$(this).attr("rel"), ptab);
		
		$(".plink", ptab).removeClass("sel");
		$(this).addClass("sel");
		
		if(subtab.length>0){
			$(".tab_childs", ptab).css("display", "none");
			subtab.css("display", "block");
		}
	},
	
	changeSubTab_click: function(){
		var ptab 		= $(this).parents(".ui_tab_control"),
		idtabsel	= "#tchild"+$(".tab_parent a.sel", ptab).attr("rel");
		
		$(idtabsel+" .clink", ptab).removeClass("sel");
		$(this).addClass("sel");
	}
};



/**
 * Control Like
 */
function CLikesEvents(value){
	this.value = value;
	this.barControl_click = this.createAdvancedClickHandler(value);
}
CLikesEvents.prototype.createAdvancedClickHandler = function _MyEvents_createAdvancedClickHandler(value){
	return function(event){
		CLikesEvents.barControl_click(event || window.event, value);
	};
};
CLikesEvents.barControl_click = function _MyEvents_advancedClickHandler(event, value){	
	var vthis 		= event.target,
	obj		 	= $(event.target).parents(value.sel_parent),
	obj_id	 	= $(value.sel_item, obj).val(),
	user_id 	= $(value.sel_user, obj).val(),
	action 		= ($(event.target).is(".like")? LikeAction.LIKE: LikeAction.UNLIKE);
	if($(event.target).is(".ui_likes_sel"))
		action = LikeAction.NEUTRO;
	
	eval('$$.'+value.method+'({'+
	'	lang:			$$.conf.lang,'+
	'	consumer_key:	$$.conf.consumer_key,'+
	'	oauth_token:	$$.conf.oauth_token,'+
	'	user_id:		user_id,'+
	'	'+value.field+':		obj_id,'+
	'	action:			action,'+
	'	callback: function(data){'+
	'		if(data.status.code == 200){'+
	'			$("a.ui_like", $(vthis).parents("div.ui_likes")).removeClass("ui_likes_sel");'+
	'			if(action != LikeAction.NEUTRO){'+
	'				$(vthis).addClass("ui_likes_sel");'+
	'				$(vthis).animate({opacity: 0.4}, {'+
	'    					duration: 400,'+
	'    					complete: function(){'+
	'						$(vthis).animate({opacity: 1}, 400);'+
	'					}'+
	'				});'+
	'			}'+
	'			if(value.count_like!=null){'+
	'				var conta = $(".num_likes", $(vthis).parents(value.count_like));'+
	'				$$.like_nums({'+
	'					lang:			$$.conf.lang,'+
	'					consumer_key:	$$.conf.consumer_key,'+
	'					oauth_token:	$$.conf.oauth_token,'+
	'					table:			"items",'+
	'					id:				obj_id,'+
	'					callback: function(data){'+
	'						conta.text(data.nums_likes);'+
	'					}'+
	'				});'+
	'			}'+
	'		}'+
	'	}'+
	'});');
	$()
};

function clikes(sel_parent, sel_item, sel_user, method, field, counter){
	this.sel_parent = sel_parent?sel_parent:'';
	this.sel_item	= sel_item?sel_item:'';
	this.sel_user	= sel_user?sel_user:'';
	this.method		= method?method:'';
	this.field		= field?field:'';
	this.count_like	= counter?counter: null;
	this.events 	= new CLikesEvents(this);
	clikes.params = {
		sel_parent 	: this.sel_parent,
		sel_item	: this.sel_item,
		sel_user	: this.sel_user,
		method		: this.method,
		field		: this.field
	};
	//$(".ui_like").live('click', this.barControl_click);
}
clikes.prototype.setVals = function(obj){
	try{
		//for(var item = 0; item < obj.length; item++){
		for(var item in obj){
			if(typeof obj[item] == 'string')
				eval("this."+item+" = '"+obj[item]+"'");
			else
				eval("this."+item+" = "+obj[item]);
		}
	}catch(e){}
};
clikes.prototype.setVal = function(name, val){
	try{
		eval("this."+name+" = "+val);
	}catch(e){}
};
clikes.prototype.getVal = function(name){
	return eval("this."+name);
};





/**
 * Control rankear
 */
var crankear = {
	ini: function(){
		$(".rankear").live('click', crankear.barControl_click);
	},
	
	barControl_click: function(){
		if($(".ul_rank", $(this).parent()).is(":visible")){
			$(".ul_rank", $(this).parent()).remove();
		}else{
			var vthis 		= this,
			postyle 	= $(this).parents("div.postyle");
			postyle = postyle.length==0? $(this).parents("#postyle_conte_detail"): postyle;
			var postyle_id 	= $("#postyle_id", postyle).val();
			
			$$.rank_query({
				lang:			$$.conf.lang,
				consumer_key:	$$.conf.consumer_key,
				callback: function(data){
					if(data.rankings.length > 0){
						var li = '';
						//for(var rank = 0; rank < data.rankings.length; rank++){
						for(var rank in data.rankings){
							li += '<li id="rank-'+data.rankings[rank].id+'" onclick="crankear.rankear(this, \''+postyle_id+'\');">'+data.rankings[rank].name+'</li>';
						}
						li = '<ul class="ul_rank">'+li+'</ul>';
						$(vthis).parent().append(li);
					}
				}
			});
		}
	},
	rankear: function(obj, postyle_id){
		var rank_id = obj.id.replace("rank-", "");
		$$.postyle_rank({
			lang:			$$.conf.lang,
			consumer_key:	$$.conf.consumer_key,
			oauth_token:	$$.conf.oauth_token,
			postyle_id:		postyle_id,
			ranking_id:		rank_id,
			callback: function(data){
				$(obj).parent().remove();
				alert(data.status.message);
			}
		});
	}
};





/**
 * Control comments
 */
var ccomments = {
	ini: function(){
		$(".acomment").live('click', ccomments.barControl_click);
		$(".frm_comment").live('submit', ccomments.comment_submit);
		$(".link_all_comment").live('click', ccomments.linkAllComment_click);
		$(".comments .comment").live({
			mouseenter: ccomments.barComment_hover,
			mouseleave: ccomments.barComment_hover
		});
		$(".comm_delete").live('click', ccomments.barCommentDelete_click);
	},
	
	barCommentDelete_click: function(){
		var pcomment	= $(this).parents("div.comment"),
		comment_id	= $("#comment_id", pcomment).val(),
		action_method	= $("#action_method", pcomment).val();
		
		$$[action_method+"_disable_comment"]({
			lang:				$$.conf.lang,
			consumer_key:		$$.conf.consumer_key,
			oauth_token:		$$.conf.oauth_token,
			comment_id:			comment_id,
			enable:				'0',
			callback: function(data){
				$(pcomment).fadeOut(500, function(){
					$(pcomment).remove();
				});
				//alert(data.status.message);
			}
		});
	},
	
	barComment_hover: function(event){
		if(event.type == "mouseenter"){
			$("ul.bardel, ul.bardel2", this).css("display", "block");
		}else{
			$("ul.bardel, ul.bardel2", this).css("display", "none");
		}
	},
	
	barControl_click: function(){
		var obj 	= $(this).parents("div.comments");
		if($(".frm_comment", obj).is(":visible"))
			$(".frm_comment", obj).fadeOut("slow");
		else{
			$(".frm_comment", obj).fadeIn("slow");
			$(".frm_comment .txt_comment", obj).focus();
		}
	},
	
	comment_submit: function(){
		var comment 	= lml.addslashes($(".txt_comment", this).val()),
		obj_comm 	= $(this).parents("div.comments"),
		obj_parent 	= $(this).parents($("#content_parent", obj_comm).val()),
		txt_obj_id	= $("#id_obj_comment", obj_comm).val(),
		obj_id 		= $("#"+txt_obj_id, obj_parent).val(),
		action_method	= $("#action_method", obj_comm).val(), jsonp;
		
		if(comment!=''){
			jsonp = {
				lang:				$$.conf.lang,
				consumer_key:		$$.conf.consumer_key,
				oauth_token:		$$.conf.oauth_token,
				comment:			comment,
				callback: function(data){
					var frm_obj = $(".frm_comment", obj_parent).fadeOut("slow");
					$(".txt_comment", frm_obj).val("");
					$(".link_all_comment", obj_parent).click();
				}
			};
			jsonp[txt_obj_id] = obj_id;
			$$[action_method+'_comment'](jsonp);
		}
		return false;
	},
	
	generateComment: function(data, postyle, return_html){
		var comms 		= $(".comments", postyle),
		txt 		= "",
		width_box 	= comms.width(),
		bar_delete	= '',
		action_method = $("#action_method", comms).val();
		
		$("div.comment", comms).remove();
		//for(var item = 0; item < data.comments.length; item++){
		for(var item in data.comments){
			if(data.comments[item].can_be_deleted == '1')
				bar_delete = '<ul class="action_bar '+(item % 2 == 0? 'bardel': 'bardel2')+'">'+
					'<li id="action_bar_li-0" class="action_bar_li">'+
					'<a href="javascript:void(0);" class="action_bar comm_delete">'+lang.deletee+'</a>'+
				'</li></ul>';
			
			txt += '<div id="ui_comments-'+item+'" class="comment">'+
				'<img src="'+(data.comments[item].images.length>0? data.comments[item].images[0].sizes[3].url: '')+'" width="30" height="30" '+
					'class="'+(item % 2 == 0? 'img': 'img2')+'">'+
				'<div  class="'+(item % 2 == 0? 'comm_conte': 'comm_conte2')+'" style="width:'+(width_box-80)+'px;">'+
					'<a href="'+$$.lml_get_url('user', data.comments[item].user_id)+'" class="link_blue">'+
						data.comments[item].name+' '+data.comments[item].last_name+'</a> '+
					data.comments[item].comment+
					'<span class="'+(item % 2 == 0? 'comm_conte_pic': 'comm_conte_pic2')+'"></span>'+
				'</div>'+
				'<div class="clear"></div><span class="'+(item % 2 == 0? 'date_comment': 'date_comment2')+'">'+
				data.comments[item].date_added+'</span>'+
				bar_delete+
				'<div class="clear"></div>'+
				'<input type="hidden" id="comment_id" value="'+data.comments[item].comment_id+'">'+
				'<input type="hidden" id="action_method" value="'+action_method+'"></div>';
		}
		if(return_html==true)
			return txt;
		else
			comms.prepend(txt);
	},
	
	linkAllComment_click: function(){
		var vthis		= this,
		obj_comm 	= $(this).parents("div.comments"),
		obj_parent 	= $(this).parents($("#content_parent", obj_comm).val()),
		txt_obj_id	= $("#id_obj_comment", obj_comm).val(),
		obj_id 		= $("#"+txt_obj_id, obj_parent).val(),
		action_method	= $("#action_method", obj_comm).val();
		
		eval('$$.'+action_method+'_get_comments({'+
		'	lang:					"'+$$.conf.lang+'",'+
		'	consumer_key:			"'+$$.conf.consumer_key+'",'+
		'	oauth_token:			"'+$$.conf.oauth_token+'",'+
		'	result_items_per_page:	"600",'+
		'	result_page:			"1",'+
		'	'+txt_obj_id+':			"'+obj_id+'",'+
		'	callback: function(data){'+
		'		ccomments.generateComment(data, obj_parent);'+
		'		$(vthis).parents(".action_bar_li").css("display", "none");'+
		'		var cula = $(".action_bar_li", $(vthis).parents(".action_bar")).length - 2;'+
		'		$("#action_bar_li-"+cula, $(vthis).parents(".action_bar")).addClass("no_border");'+
		'	}'+
		'});');
	}
};




function effects(){
	this.pagination = function(params){
		params = params? params: {
			width_item	: 190,
			num_cols	: 1,
			remove		: '',
			pfriends	: '',
			vthis		: '',
			posi		: '',
			html		: ''
		};
		
		//efectos de transicion
		var return_pos = params.width_item*params.num_cols,
		x = $(params.remove, params.pfriends).position().left,
		width = $(params.remove, params.pfriends).width();
		if($(params.vthis).is(".next")){
			params.pfriends.append(params.html);			
			var dire_pos  = new Array((x-width)+"px", (x-width-25)+"px");
			$(params.posi, params.pfriends).css("left", x+"px");
		}else{
			params.pfriends.prepend(params.html);
			$(params.posi, params.pfriends).css("left", "0px");
			$(params.remove, params.pfriends).css("left", "0px");
			var dire_pos  = new Array(width+"px", (width+25)+"px");
		}
		
		$(params.remove, params.pfriends).animate({
			left: dire_pos[0]
		}, {
			duration	: 250,
			complete	: function(){
				$(params.remove, params.pfriends).remove();
			}
		});
		$(params.posi, params.pfriends).animate({
			left: dire_pos[1]
		}, {
			duration	: 250,
			complete	: function(){
				$(params.posi, params.pfriends).css("left", return_pos);
			}
		});
	};
	
	this.gallery = function(params){
		params = params? params: {
			width_item	: 190,
			pfriends	: '',
			vthis		: ''
		};
		
		//efectos de transicion
		var x = params.pfriends.position().left,
		return_pos = 0;
		
		if($(params.vthis).is(".next")){
			x -= params.width_item+25;
			return_pos = x+25;
		}else{
			x += params.width_item+25;
			return_pos = x-25;
		}
		
		params.pfriends.animate({
			left: x
		}, {
			duration	: 250,
			complete	: function(){
				params.pfriends.css("left", return_pos);
			}
		});
	};
};


var cNotification = {
	timeout: undefined,
	paginar: undefined,
	ini: function(){
		cNotification.paginar = new pagination();
	
		$(".notifi_num").toggle(cNotification.showNotify_click, function(){
			clearTimeout(cNotification.timeout);
			$("#show_notifys").hide('normal');
			$(this).parents(".notificaciones").css('background-color', "transparent");
		});
		
		$(".notifiOpenPreview").live('click', cNotification.openPreview_click);
		$(".notifica_info .friend_acept, " +
			".notifica_info .friend_deny").live('click', cNotification.responseFriends_click);
		$(".notify_seeall").live('click', function(){
			window.location = $$.lml_get_url('notifis', '');
		});
		cNotification.setColorsNotifu();
		
		$(".col_content #ui_pagination .pag_notify").live('click', cNotification.getNotifys_click);
		
		setTimeout(cNotification.refresh_notification, 20000);
	},
	
	refresh_notification: function(){
		$$.notification_num_notification({
			lang:				$$.conf.lang,
			consumer_key:		$$.conf.consumer_key,
			oauth_token:		$$.conf.oauth_token,
			callback: function(data){
				$(".notifi_num").text(data.num_notification);
				if(data.num_notification > 0)
					$(".notifi_num").addClass("notifi_alert");
				else
					$(".notifi_num").removeClass("notifi_alert");
				setTimeout(cNotification.refresh_notification, 10000);
			}
		});
	},
	
	showNotify_click: function(){
		var vthis = this;
		clearTimeout(cNotification.timeout);
		$$.notification_get({
			lang:				$$.conf.lang,
			consumer_key:		$$.conf.consumer_key,
			oauth_token:		$$.conf.oauth_token,
			result_items_per_page:	'5',
			result_page:		'1',
			output_format:		'1',
			callback: function(data){
				var html = '', item;
				for(item in data.notifications){
					html += '<div class="notify_item">'+data.notifications[item]+'<div class="clear"></div></div>';
				}
				html += '<div class="notify_seeall">'+lang.see_all+'</div>';
				$(vthis).parents(".notificaciones").css('background-color', "#fff");
				$("#show_notifys").html(html).show('normal');
				$("body").append('<div id="layer_back_notifys">&ensp;</div>'); //div para poder cerrar las notifis al dar click afuera
				$("#layer_back_notifys").one('click', function(){
					$(".notifi_num").click();
					$(this).remove();
				});
				cNotification.setNumNotif(data.num_notification);
				
				cNotification.setColorsNotifu();
				
				cNotification.timeout = setTimeout(function(){
					$$.notification_see({
						lang:				$$.conf.lang,
						consumer_key:		$$.conf.consumer_key,
						oauth_token:		$$.conf.oauth_token,
						callback: function(data){
							cNotification.setNumNotif(data.num_notification);
						}
					});
				}, 4000);
			}
		});
	},
	
	getNotifys_click: function(){
		var vthis = this, pitems = $(this).parents(".col_content"), 
		pag = $(this).attr("rel"), per_pag=15;
		$$.notification_get({
			lang:				$$.conf.lang,
			consumer_key:		$$.conf.consumer_key,
			oauth_token:		$$.conf.oauth_token,
			result_items_per_page:	per_pag,
			result_page:		pag,
			output_format:		'1',
			pagination:			'y',
			callback: function(data){
				var leng = $(".ponaqui").length,
				remove	  	= (leng > 0)? "div.ponaqui": "div.ponaqui2",
				posi	  	= (leng > 0)? "div.ponaqui2": "div.ponaqui",
				html = '<div class="'+posi.substr(4)+'" style="float:left;position: relative;width:680px; left: 1360px;">', item;
			
				for(item in data.notifications){
					html += '<div class="notify_item">'+data.notifications[item]+'<div class="clear"></div></div>';
				}
				html += '</div>';
				$(vthis).parents(".notificaciones").css('background-color', "#fff");
				//$("#notifications_detail").html(html);
				var peffect = new effects();
				peffect.pagination({
					width_item	: 680,
					num_cols	: 1,
					remove		: remove,
					pfriends	: $("#notify_conte"),
					vthis		: vthis,
					posi		: posi,
					html		: html
				});
				
				cNotification.setColorsNotifu();
				
				cNotification.paginar.setVals({
					total_rows 		: data.num_notification,
					current_page	: pag,
					per_page		: per_pag,
					pag_class		: 'pag_notify',
					obj_id			: "ui_pagination"
				});
				cNotification.paginar.render();
			}
		});
	},
	
	setColorsNotifu: function(){
		$(".notifica_info.sinver").each(function(){
			var tss = $(this), parren = tss.parents(".notify_item");
			parren.css("background-color", "#FEF2DA");
			tss.removeClass("sinver");
			if($(".friend_acept", this).length > 0)
				parren.addClass("sinver");
		});
	},
	setNumNotif: function(num){
		$(".notifi_num").text(num).removeClass("notifi_alert");
		if(num != '0')
			$(".notifi_num").addClass("notifi_alert");
		$(".notify_item").css("background-color", 'transparent');
		$(".notify_item.sinver").css("background-color", '#FEF2DA');
	},
	
	responseFriends_click: function(){
		var vthis = $(this), user_id=vthis.attr("me_id"), friend_id=vthis.attr("id"), 
			paren = vthis.parents('.notifica_info'), action, params = {
			lang:				$$.conf.lang,
			consumer_key:		$$.conf.consumer_key,
			oauth_token:		$$.conf.oauth_token,
			user_id:			user_id,
			friend_id:			friend_id,
			action:				'response',
			action_response:	'',
			callback: function(data){
				$("#noti_msg", paren).html('').text(data.status.message);
			}
		};
		if(vthis.is(".friend_acept"))
			params['action_response'] = 'accept';
		else
			params['action_response'] = 'deny';
		
		$$.user_friend_invite(params);
		$("#noti_msg", paren).html('<img src="'+$$.conf.url_base+'application/images/loader_min.gif" width="16" height="16">');
		
		vthis.parents('.notify_item').each(function(){
			$(this).removeClass('sinver').css("background-color", 'transparent');
		});
		$(".friend_acept, .friend_deny", paren).remove();
	},
	
	openPreview_click: function(){
		switch($(this).attr("obj_type")){
			case "brand": cBrand.openWinPreview_click(this); break;
			case "item": citems.openWinDetail_click(this); break;
			case "store": cStore.openWinPreview_click(this); break;
		}
		if($("#show_notifys").css("display") == 'block')
			$(".notifi_num").click();
	}
};







/**google map**/
var map,
marker;
function initialize(){
	geocoder = new google.maps.Geocoder();
	var latlon = new google.maps.LatLng(21.289374, -101.689453),
	myOptions = {
		zoom: 5,
		center: latlon,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map = new google.maps.Map(document.getElementById("wrap_mapa"), myOptions);
	
	marker = new google.maps.Marker({
		map: map,
		draggable: true,
		position: latlon
	});
}


var timezone = {
	ini: function(){
		//timezone
		var now = new Date(),
		coo_expires = new Date(now.getTime()+1000*60*60*24);
		lml.setCookie("lml_tzone", timezone.getTimeZone(), coo_expires, "/");
	},
	getTimeZone: function () {
		var rightNow = new Date(),
		jan1 = new Date(rightNow.getFullYear(), 0, 1, 0, 0, 0, 0),  // jan 1st
		june1 = new Date(rightNow.getFullYear(), 6, 1, 0, 0, 0, 0), // june 1st
		temp = jan1.toGMTString(),
		jan2 = new Date(temp.substring(0, temp.lastIndexOf(" ")-1));
		temp = june1.toGMTString();
		var june2 = new Date(temp.substring(0, temp.lastIndexOf(" ")-1)),
		std_time_offset = (jan1 - jan2) / (1000 * 60 * 60),
		daylight_time_offset = (june1 - june2) / (1000 * 60 * 60),
		dst;
		if (std_time_offset == daylight_time_offset) {
			dst = "0"; // daylight savings time is NOT observed
		} else {
			// positive is southern, negative is northern hemisphere
			var hemisphere = std_time_offset - daylight_time_offset;
			if (hemisphere >= 0)
				std_time_offset = daylight_time_offset;
			dst = "1"; // daylight savings time is observed
		}
		return timezone.convert(std_time_offset); //+","+dst;
	},

	convert: function(value) {
		var hours = parseInt(value);
	   	value -= parseInt(value);
		value *= 60;
		var mins = parseInt(value);
	   	value -= parseInt(value);
		value *= 60;
		var secs = parseInt(value),
		display_hours = hours;
		// handle GMT case (00:00)
		if (hours == 0) {
			display_hours = "00";
		} else if (hours > 0) {
			// add a plus sign and perhaps an extra 0
			display_hours = (hours < 10) ? "+0"+hours : "+"+hours;
		} else {
			// add an extra 0 if needed 
			display_hours = (hours > -10) ? "-0"+Math.abs(hours) : hours;
		}
		
		mins = (mins < 10) ? "0"+mins : mins;
		return display_hours+":"+mins;
	}
};
