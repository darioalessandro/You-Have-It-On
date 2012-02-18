var $$ = {
	conf: {
		method: 			"POST",
		format:				"json",
		url_base:			"http://localhost/lml/",
		url_base_service:	"services/",
		cssct:				"",
		consumer_key:		"89c1cd99a5ed1f0362668d15c15cb58104d7675fc",
		oauth_token:		"a16029b067f18a0900871779e3a314fc04d76773c",
		lang:				"spa"
	},
	
	set_config: function(obj){
		try{
			for(var item in obj){
				/*if(typeof obj[item] == 'string')
					$$.conf[item] = obj[item];
				else*/
				$$.conf[item] = obj[item];
			}
			$$.conf.url_base_service = $$.conf.url_base+$$.conf.url_base_service;
		}catch(e){}
	},
	
	facebook_importFriends: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token",
				valida: "1,1,1",
				type:	"s,s,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("facebook/importFriends", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},

	user_register: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,consumer_secret,name,sex,email,password,confirm_password",
				valida: "1,1,1,1,1,1,1,0",
				type:	"s,s,s,s,s,s,s,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("user/register", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	user_login: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,email,password",
				valida: "1,1,1,1",
				type:	"s,s,s,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("user/login_user", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	user_query: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token",
				valida: "1,1,1",
				type:	"s,s,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("user/query", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	user_edit: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,name,last_name,sex,email,password,birthday,state",
				valida: "1,1,1,0,0,0,0,0,0,0",
				type:	"s,s,s,s,s,s,s,s,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("user/edit", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	user_friend_invite: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,user_id,friend_id,action,action_response",
				valida: "1,1,1,1,1,1,0",
				type:	"s,s,s,n,n,s,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("user/friend_invite", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	user_friend_remove: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,user_id,friend_id",
				valida: "1,1,1,1,1",
				type:	"s,s,s,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("user/friend_remove", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	user_friends: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,result_items_per_page,result_page,user_id,pending",
				valida: "1,1,1,1,1,1,0",
				type:	"s,s,s,n,n,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("user/friends", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	like_nums: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,table,id",
				valida: "1,1,1,1,1",
				type:	"s,s,s,s,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("user/like_nums", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	user_like_store: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,user_id,store_id,action",
				valida: "1,1,1,1,1,1",
				type:	"s,s,s,n,n,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("user/like_store", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	user_like_brand: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,user_id,brand_id,action",
				valida: "1,1,1,1,1,1",
				type:	"s,s,s,n,n,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("user/like_brand", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	user_like_items: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,user_id,item_id,action",
				valida: "1,1,1,1,1,1",
				type:	"s,s,s,n,n,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("user/like_items", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	user_like_postyle: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,user_id,postyle_id,action",
				valida: "1,1,1,1,1,1",
				type:	"s,s,s,n,n,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("user/like_postyle", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	country_query: function(params){
		var valida_field = {
				field: 	"lang,consumer_key",
				valida: "1,1",
				type:	"s,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("country/query", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	states_query: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,country",
				valida: "1,1,1",
				type:	"s,s,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("states/query", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	brand_add: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,name,description,country",
				valida: "1,1,1,1,0,0",
				type:	"s,s,s,s,s,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("brand/add", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	brand_edit: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,brand_id,name,description,country",
				valida: "1,1,1,1,1,0,0",
				type:	"s,s,s,n,s,s,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("brand/edit", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	brand_remove_image: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,image_id,enable",
				valida: "1,1,1,1,1",
				type:	"s,s,s,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("brand/remove_image", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	brand_query: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,result_items_per_page,result_page,filter_country,filter_brand_id,filter_name,filter_search_text,return_comments",
				valida: "1,1,0,1,1,0,0,0,0,0",
				type:	"s,s,s,n,n,s,n,s,s,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("brand/query", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	store_add: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,name,lat,lon,store_id,description,location_description,location_street_name,location_city,location_state,location_postal_code,location_country,day_id,open_hour,close_hour",
				valida: "1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0",
				type:	"s,s,s,s,n,n,n,s,s,s,s,s,s,s,a,a,a"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("store/add", $$.lml_parser(params), params.callback, 'txt');
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	store_edit: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,store_id,name,lat,lon,principal,description,location_description,location_street_name,location_city,location_state,location_postal_code,location_country,day_id,open_hour,close_hour,days_range_id",
				valida: "1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0",
				type:	"s,s,s,n,s,n,n,n,s,s,s,s,s,s,s,a,a,a,a"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("store/edit", $$.lml_parser(params), params.callback, 'txt');
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	store_query: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,result_items_per_page,result_page,filter_store_location_id,filter_name,filter_search_text,filter_lat,filter_lon,filter_radio,return_comments",
				valida: "1,1,0,1,1,0,0,0,0,0,0,0",
				type:	"s,s,s,n,n,n,s,s,n,n,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("store/query", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	store_remove_image: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,image_id,enable",
				valida: "1,1,1,1,1",
				type:	"s,s,s,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("store/remove_image", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	attr_add: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,name",
				valida: "1,1,1,1",
				type:	"s,s,s,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("attr/add", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	attr_query: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,result_items_per_page,result_page,filter_attr_id,filter_category_id,filter_name,filter_lang,filter_type",
				valida: "1,1,1,1,1,0,0,0,0,0",
				type:	"s,s,s,n,n,n,n,s,s,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("attr/query", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	category_add: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,name,category_parent,cat_attr",
				valida: "1,1,1,1,0,0",
				type:	"s,s,s,s,n,a"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("category/add", $$.lml_parser(params), params.callback, 'txt');
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	category_query: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,result_items_per_page,result_page,filter_category_id,filter_name,filter_lang,filter_type,filter_recursive,filter_category_parent",
				valida: "1,1,1,1,1,0,0,0,0,0,0",
				type:	"s,s,s,n,n,n,s,s,n,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("category/query", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	item_add: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,label,cat_attr_id,value,brand_id,bought_in,price,brand_name,bought_label,bought_lat,bought_lon",
				valida: "1,1,1,1,0,0,0,0,0,0,0,0,0",
				type:	"s,s,s,s,a,a,n,n,n,s,s,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("item/add", $$.lml_parser(params), params.callback, 'txt');
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	item_edit: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,item_id,label,cat_attr_id,value,brand_id,bought_in,price",
				valida: "1,1,1,1,1,1,1,0,0,0",
				type:	"s,s,s,n,s,a,a,n,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("item/edit", $$.lml_parser(params), params.callback, 'txt');
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	item_query: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,result_items_per_page,result_page,filter_item_id,filter_label,filter_store_location_id,filter_brand_id,filter_category_id,filter_user_id,filter_lat,filter_lon,filter_radio,filter_postyle_id,return_comments,get_light_result,only_my_items",
				valida: "1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0",
				type:	"s,s,s,n,n,n,s,n,n,n,n,n,n,n,n,n,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("item/query", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	item_remove_image: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,image_id,enable",
				valida: "1,1,1,1,1",
				type:	"s,s,s,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("item/remove_image", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	postyle_disable: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,postyle_id,enable",
				valida: "1,1,1,1,1",
				type:	"s,s,s,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("postyle/disable", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	postyle_rank: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,postyle_id,ranking_id",
				valida: "1,1,1,1,1",
				type:	"s,s,s,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("postyle/rank", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	postyle_comment: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,postyle_id,comment",
				valida: "1,1,1,1,1",
				type:	"s,s,s,n,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("postyle/comment", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	postyle_disable_comment: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,comment_id,enable",
				valida: "1,1,1,1,1",
				type:	"s,s,s,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("postyle/disable_comment", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	ocation_add: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,name",
				valida: "1,1,1,1",
				type:	"s,s,s,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("ocation/add", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	ocation_query: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,result_items_per_page,result_page,filter_ocation_id,filter_name,filter_lang,filter_type",
				valida: "1,1,1,1,1,0,0,0,0",
				type:	"s,s,s,n,n,n,s,s,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("ocation/query", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	postyle_add: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,image_url,description,items_id,tag_description,tag_width,tag_height,tag_x,tag_y,ocation_id,location_lat,location_lon,ocation_label",
				valida: "1,1,1,1,0,0,0,0,0,0,0,0,0,0,0",
				type:	"s,s,s,s,s,a,a,a,a,a,a,n,n,n,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("postyle/add", $$.lml_parser(params), params.callback, 'txt');
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	postyle_query: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,result_items_per_page,result_page,filter_postyle_id,filter_ranking_id,filter_ocation_id,filter_user_id,filter_items,filter_location_lat,filter_location_lon,return_comments,filter_order,filter_both",
				valida: "1,1,1,1,1,0,0,0,0,0,0,0,0,0,0",
				type:	"s,s,s,n,n,n,n,n,n,a,n,n,n,s,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("postyle/query", $$.lml_parser(params), params.callback, 'txt');
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	postyle_get_comments: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,result_items_per_page,result_page,postyle_id",
				valida: "1,1,1,1,1",
				type:	"s,s,n,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("postyle/get_comments", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	accessories_comment: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,accessories_user_id,comment",
				valida: "1,1,1,1,1",
				type:	"s,s,s,n,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("accessories/comment", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	accessories_disable_comment: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,comment_id,enable",
				valida: "1,1,1,1,1",
				type:	"s,s,s,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("accessories/disable_comment", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	accessories_get_comments: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,result_items_per_page,result_page,user_id",
				valida: "1,1,1,1,1",
				type:	"s,s,n,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("accessories/get_comments", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	item_comment: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,item_id,comment",
				valida: "1,1,1,1,1",
				type:	"s,s,s,n,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("item/comment", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	item_disable_comment: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,comment_id,enable",
				valida: "1,1,1,1,1",
				type:	"s,s,s,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("item/disable_comment", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	item_get_comments: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,result_items_per_page,result_page,item_id",
				valida: "1,1,1,1,1",
				type:	"s,s,n,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("item/get_comments", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	brand_comment: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,brands_id,comment",
				valida: "1,1,1,1,1",
				type:	"s,s,s,n,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("brand/comment", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	brand_disable_comment: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,comment_id,enable",
				valida: "1,1,1,1,1",
				type:	"s,s,s,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("brand/disable_comment", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	brand_get_comments: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,result_items_per_page,result_page,brands_id",
				valida: "1,1,1,1,1",
				type:	"s,s,n,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("brand/get_comments", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	store_comment: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,store_id,comment",
				valida: "1,1,1,1,1",
				type:	"s,s,s,n,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("store/comment", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	store_disable_comment: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,comment_id,enable",
				valida: "1,1,1,1,1",
				type:	"s,s,s,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("store/disable_comment", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	store_get_comments: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,result_items_per_page,result_page,store_id",
				valida: "1,1,1,1,1",
				type:	"s,s,n,n,n"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("store/get_comments", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	rank_query: function(params){
		var valida_field = {
				field: 	"lang,consumer_key",
				valida: "1,1",
				type:	"s,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("rank/query", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	user_config: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,config_id,values",
				valida: "1,1,1,1,1",
				type:	"s,s,s,a,a"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("user/config", $$.lml_parser(params), params.callback, 'txt');
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	user_get_config: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token",
				valida: "1,1,1",
				type:	"s,s,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("user/get_config", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	config_query: function(params){
		var valida_field = {
				field: 	"lang,consumer_key",
				valida: "1,1",
				type:	"s,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("config/query", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	notification_get: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token,result_items_per_page,result_page,output_format,pagination",
				valida: "1,1,1,1,1,0,0",
				type:	"s,s,s,n,n,n,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("notification/get", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	notification_num_notification: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token",
				valida: "1,1,1",
				type:	"s,s,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("notification/num_notification", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	notification_see: function(params){
		var valida_field = {
				field: 	"lang,consumer_key,oauth_token",
				valida: "1,1,1",
				type:	"s,s,s"
			};
		
		res = this.lml_validate(params, valida_field);
		if(res.status.code == 200){
			$$.lml_ajax("notification/see", params, params.callback);
		}else{
			if($.isFunction(params.callback))
				params.callback.call(this, res);
		}
	},
	
	
	
	
	lml_validate: function(params, valf){
		var field = valf.field.split(","), valida = valf.valida.split(","), type = valf.type.split(","), band = 200, auxi = 0;
		for(var i=0;i<field.length;i++){
			switch(type[i]){
				case "s": band = $$.is_string(params[field[i]]); break;
				case "n": band = $$.is_numeric(params[field[i]]); break;
				case "a": band = $$.is_array(params[field[i]]); break;
			}
			if(valida[i]=="0" && band==46)
				band = 200;
			else if(band == 46)
				band = 45;
			if(band==45){
				auxi = i;
				break;
			}
		}
		
		msg = "Successful";
		if(band == 45)
			msg = "Error in "+field[auxi];
		
		return { 
			status: {  
				code: band, 
				message: msg
			}
		};
	},
	
	lml_parser: function(arr, campo, level, con) {
		var txt_par = '';
		if(!level) con = 1;
		if(!level) level = '';
		if(!campo) campo = '';
		
		if(typeof(arr) == 'object') { 
			for(var item in arr) {
				var value = arr[item];
				
				if(typeof(value) == 'object') {
					txt_par += $$.lml_parser(value, item, '[]', con);
				}else{
					if(campo != 'callback' && item != 'callback'){
						if(campo != '')
							txt_par += (con>1?"&":"") + campo + level + "=" + encodeURIComponent(value);
						else
							txt_par += (con>1?"&":"") + item + level + "=" + encodeURIComponent(value);
					}
				}
				con++;
			}
		}
		
		return txt_par;
	},
	
	dump: function(arr,level) {
		var dumped_text = "";
		if(!level) level = 0;
		
		var level_padding = "";
		for(var j=0;j<level+1;j++) level_padding += "    ";
		
		if(typeof(arr) == 'object') { 
			for(var item in arr) {
				var value = arr[item];
				
				if(typeof(value) == 'object') {
					dumped_text += level_padding + "'" + item + "' ...\n";
					dumped_text += $$.dump(value,level+1);
				} else {
					dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
				}
			}
		} else {
			dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
		}
		return dumped_text;
	},
	
	
	is_numeric: function(input){
		if(input == undefined)
			return 46;
		input = $.trim(input);
		
		if(isNaN(parseFloat(input)))
			return 45;
		return 200;
	},
	is_string: function(input){
		if(input == undefined)
			return 46;
		input = $.trim(input);
		return (input.length>0? 200: 45);
	},
	is_array: function(input){
		if(input == undefined)
			return 46;
		return ((typeof(input)=='object'&&(input instanceof Array))? 200: 45);
	},
	
	
	lml_ajax: function(url_api, params, callback, type){
		params.callback = undefined;
		if(type)
			$$.lml_ajax_str(url_api, params, callback, type);
		else
			$$.lml_ajax_json(url_api, params, callback, type);
	},
	lml_ajax_str: function(url_api, params, callback, type){
		$.ajax({
			type: 		$$.conf.method,
			url: 		$$.conf.url_base_service+url_api+"."+$$.conf.format,
			data: 		params,
			dataType:	$$.conf.format,
			success: 	function(res){
				if($.isFunction(callback))
					callback.call(this, res);
			},
			error: function(jqXHR, textStatus, errorThrown){
				//alert(textStatus);
			}
		});
	},
	lml_ajax_json: function(url_api, params, callback, type){
		$.ajax({
			type: 		$$.conf.method,
			url: 		$$.conf.url_base_service+url_api+"."+$$.conf.format,
			data: 		(params),
			dataType:	$$.conf.format,
			success: 	function(res){
				if($.isFunction(callback))
					callback.call(this, res);
			},
			error: function(jqXHR, textStatus, errorThrown){
				//alert(textStatus);
			}
		});
	},
	
	lml_get_url: function(id, val){
		switch(id){
			case 'postyle': return $$.conf.url_base+"postyle?lmlid="+val; break;
			case 'more_friends': return $$.conf.url_base+"my_friends?lmlid="+val; break;
			case 'brand': return $$.conf.url_base+"brands/"+val; break;
			case 'user': return $$.conf.url_base+"?lmlid="+val; break;
			case 'store': return $$.conf.url_base+"stores/"+val; break;
			case 'item': return $$.conf.url_base+"item?lmlid="+val; break;
			case 'notifis': return $$.conf.url_base+"notifications/"; break;
		}
	}
},



Language = {
	SPA : "spa",
	FRE : "fre",
	ENG : "eng"
}, 
Sex = {
	MAN 	: "m",
	WOMAN 	: "f"
}, 
FriendAction = {
	INVITE 	 : "invite",
	RESPONSE : "response"
},
FriendActionResponse = {
	ACCEPT 	: "accept",
	DENY 	: "deny"
},
LikeAction = {
	LIKE 	: "like",
	UNLIKE 	: "unlike",
	NEUTRO	: "neutro"
},
Format = {
	JSON : "json"
},




Url = {
	// public method for url encoding
	encode : function (string) {
		return escape(this._utf8_encode(string));
	},
 
	// public method for url decoding
	decode : function (string) {
		return this._utf8_decode(unescape(string));
	},
 
	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "", n, c;
 
		for (n = 0; n < string.length; n++) {
 
			c = string.charCodeAt(n);
 
			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
 
		}
 
		return utftext;
	},
 
	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "", i = 0, c = 0, c2 = 0, c3 = 0;
 
		while ( i < utftext.length ) {
 
			c = utftext.charCodeAt(i);
 
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
 
		}
 
		return string;
	}
},







lml = {
	isset: function(variable_name){
		try {
			if (typeof(eval(variable_name)) != 'undefined')
				if (eval(variable_name) != null)
					return true;
		}catch(e) { }
		return false;
   },
   
   addslashes: function(str) {
	   str = $.trim(str);
	   str=str.replace(/\\/g,'\\\\');
	   str=str.replace(/\'/g,'\\\'');
	   str=str.replace(/\"/g,'\\"');
	   str=str.replace(/\0/g,'\\0');
	   return str;
   },
   
   setCookie: function(cookieName,cookieValue,expires,path,domain,secure) { 
	   document.cookie= 
	   escape(cookieName)+'='+escape(cookieValue) 
	   +(expires?'; EXPIRES='+expires.toGMTString():'') 
	   +(path?'; PATH='+path:'') 
	   +(domain?'; DOMAIN='+domain:'') 
	   +(secure?'; SECURE':''); 
   }, 

   getCookie: function(cookieName) { 
	   var cookieValue=null, posValue, endPos,  
	   posName=document.cookie.indexOf(escape(cookieName)+'='); 
	   if (posName!=-1) { 
		   posValue=posName+(escape(cookieName)+'=').length; 
		   endPos=document.cookie.indexOf(';',posValue); 
		   if (endPos!=-1) cookieValue=unescape(document.cookie.substring(posValue,endPos)); 
		   else cookieValue=unescape(document.cookie.substring(posValue)); 
	   } 
	   return cookieValue; 
   },
   
	


   
   
	error_load_img: function(){
	   $(".upload_pst_loader").css("display", "none");
    },
	load_img: function(params){
		$(".upload_pst_loader, .ifr_img_postyle").css("display", "none");
		$("#img_upload_file").val(params.file_url);
		
		var obj_img = $("#add_img_postyle"), width = 0, height = 0, left = 0;
		
		if(params.file_width > 660){
			width = 660;
			var scal = width*100/params.file_width;
			height = parseInt(scal*params.file_height/100);
		}else{
			width = params.file_width;
			height = params.file_height;
			left = parseInt((660 - params.file_width)/2);
		}
		lml.width_img = width;
		lml.height_img = height;
		
		obj_img.attr("width", width).attr("height", height).css("left", left+"px");
		obj_img.attr("src", params.file_url).imgAreaSelect({
			handles: true,
	        fadeSpeed: 200, 
	        onSelectChange: lml.preview_img,
	        onSelectEnd: lml.asigAutoCompleItem,
	        onCancelSelection: lml.cancelSelection/*,
	        onClickSelect: lml.clickSelect*/
		});
		obj_img.unbind("click", lml.clickSelect);
		obj_img.click(lml.clickSelect);
		
		$(".img_view_tag").css("display", "block");
		
		$("#postyle_add_tag").click(lml.postyle_add_tag);
	},
	
	width_img: 0,
	height_img: 0,
	preview_img: function(img, selection) {
	    if (!selection.width || !selection.height)
	        return;
	    //asignar valores del area seleccionada
	    //var scaleX = 100 / selection.width;
	    //var scaleY = 100 / selection.height;
	    var swidth = parseInt((selection.width*100)/lml.width_img),
	    sheight = parseInt((selection.height*100)/lml.height_img),
	    sx = parseInt((selection.x1*100)/lml.width_img),
	    sy = parseInt((selection.y1*100)/lml.height_img);
	 
	    $('#x1').val(sx);
	    $('#y1').val(sy);
	    $('#w').val(swidth);
	    $('#h').val(sheight);
	},
	asigAutoCompleItem: function(img, selection){
		var txt_pre = $("#items_aucomplete1").val();
		$("#img_autocomplete").remove();
		var select = $(".imgareaselect-selection").parents("div");
		
		$("body").append('<div id="img_autocomplete" style="left:'+select.position().left+'px;top:'+(select.position().top+select.height())+
				'px;"><div class="taggeo_group">'+
				'<label id="lbltype_aucomplete"><input type="radio" id="type_aucomplete1" name="type_aucomplete" checked="checked"> Items</label>'+
				'<label id="lbltype_aucomplete"><input type="radio" id="type_aucomplete2" name="type_aucomplete"> '+lang.friends+'</label><br>'+
				'<input type="text" id="items_aucomplete1" name="items" class="txt_itemauto" autocomplete="off"></div>'+
				'<input type="hidden" id="hid_itm_autocom" name="hid_itm_autocom" value="1">'+
				'<div id="items_aucomplete11" class="auto_result">'+
						'<input type="hidden" id="imgitem_id_auto" class="pst_ponid" name="item_auto">'+
				'</div></div>');
		$("#items_aucomplete1").val(txt_pre).focus();
		citems.asigAutocomplete();
		
		$("#type_aucomplete1").click(lml.taggeoChangeType_click);
		$("#type_aucomplete2").click(lml.taggeoChangeType_click);
	},
	taggeoChangeType_click: function(){
		if($(this).attr('id') == 'type_aucomplete1'){
			$("#hid_itm_autocom").val("1");
		}else{
			$("#hid_itm_autocom").val("0");
		}
		//$("#items_aucomplete1").keydown();
		citems.asigAutocomplete();
		$("#items_aucomplete1").keydown().focus();
	},
	cancelSelection: function(img, selection){
		$("#img_autocomplete").remove();
		$("#conte_mapa").css("top", "-1000px");
	},
	clickSelect: function(event){
		var objimg = $(this).imgAreaSelect({ instance: true }),
		position = lml.getPositionClick(event, this, objimg);
		objimg.setSelection(position.inix, position.iniy, position.finx, position.finy);
		objimg.setOptions({ show: true });
		objimg.update();
		lml.preview_img(this, objimg.getSelection());
		lml.asigAutoCompleItem(null, null);
	},
	getPositionClick: function(event, objimg, iiimg){
		var position = {
			inix: iiimg.evX(event),
			iniy: iiimg.evY(event),
			finx: 0,
			finy: 0
		},
		position_img = {
			inix: iiimg.viewX(0),
			iniy: iiimg.viewY(0),
			finx: $(objimg).width(),
			finy: $(objimg).height()
		};
		position.finx = position.inix+50;
		position.finy = position.iniy+50;
		
		position.inix -= position_img.inix+50;
		position.iniy -= position_img.iniy+50;
		position.finx -= position_img.inix;
		position.finy -= position_img.iniy;
		
		position_img.inix -= position_img.inix;
		position_img.iniy -= position_img.iniy;
		
		if(position.inix < position_img.inix){
			position.finx += position_img.inix - position.inix;
			position.inix = position_img.inix;
		}
		if(position.iniy < position_img.iniy){
			position.finy += position_img.iniy - position.iniy;
			position.iniy = position_img.iniy;
		}
		
		if(position.finx > position_img.finx){
			position.inix -= position.finx - position_img.finx;
			position.finx = position_img.finx;
		}
		if(position.finy > position_img.finy){
			position.iniy -= position.finy - position_img.finy;
			position.finy = position_img.finy;
		}
		return position;
	}
},


clang = {
	set: function(url, lang){
		var now = new Date(),
		coo_expires = new Date(now.getTime()+1000*60*60*24*3);
		lml.setCookie("lml_lang", lang, coo_expires, "/");
		window.location = url;
	}
};




$(document).ready(function(){
	//Buscador autocomplete
	$("#txtsearsh").inputLabel($("#hed_lblbuscar"));
	$('input[id^="txtsearsh"]').each(function(){
		var auto = new search(this, {
			url_service: $$.conf.url_base_service+"search/query.json",
			callback: function(data, obj){
				$(this).removeClass("load");
				if(data == 'sel_enter'){
					switch($(obj).attr("obj_type")){
						case "brand": cBrand.openWinPreview_click(obj); break;
						case "item": citems.openWinDetail_click(obj); break;
						case "store": cStore.openWinPreview_click(obj); break;
						case "user": window.location = $$.lml_get_url('user', $(obj).attr("id")); break;
					}
				}
			}
		},{
			lang:					$$.conf.lang,
			consumer_key:			$$.conf.consumer_key,
			oauth_token:			$$.conf.oauth_token,
			result_items_per_page:	'8',
			result_page:			'1',
			search_text:			''
		});
	});
});





/**
 * Buscar Autocompletar
 */
function search(obj, conf, params){
	this.params = params;
	var vthis = this;
	
	this.proto = {
		len_search: 2, //numero minimi de caracteres para que se lanse la busqueda
		searching: undefined, //timer para realizar los filtros de busqueda
		delay: 300, //tiempo de espera para realizar las busquedas
		term: "", //es el creterio por el que se filtraran los datos en la busqueda
		url_service: "",
		name_array: "results",
		field_filter: "search_text",
		field_par_name: "name",
		obj_tab: "",
		call_in_enter: true,
		call_enter_all: false,
		call_on_result: true,
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
					/*if(vthis.proto.call_enter_all && seless.length == 0)
						vthis.proto.callback.call(this, "sel_enter");
					*/
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
							$(objtarget).addClass("load");
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
												vthis.proto.callback.call(objtarget, "result");
										}else{
											vthis.proto.callback.call(objtarget, "no_result");
											vthis.proto.blok = false;
										}
									}
								},
								error: function(jqXHR, textStatus, errorThrown){
									alert(textStatus);
								}
							});
						}else
							vthis.proto.callback.call(objtarget, "no_result");
					}, vthis.proto.delay);
				break;
			}
		},
		renderItems: function(source){
			var objj = $(vthis.proto.obj_intput),
			stritems = '<ul class="ui_autocomplete search" style="top:0px; left:'+objj.position().left+'px; width:'+(objj.width()+50)+'px;">',
			htm, item, inx=2;
			for(item in source){
				htm = '<div class="search_img">';
				if(source[item].type == 'user')
					inx = 3;
				if(source[item].images.length > 0)
					htm += '<img src="'+source[item].images[inx].url+'" width="40" height="40">';
				htm += '</div><div class="search_info"><strong>'+source[item].label+'</strong><br>';
				switch(source[item].type){
					case 'item': 
						htm += source[item].brand!=''? '<span>'+lang.brand+':</span> '+source[item].brand+' ': '';
						htm += source[item].price!=''? '<span>'+lang.price+':</span> '+source[item].price+' ': '';
						htm += source[item].store!=''? '<span>'+lang.store+':</span> '+source[item].store+' ': '';break;
					case 'store': htm += source[item].address; break;
					case 'brand': break;
				}
				htm += '<div class="clear"></div></div>';
				stritems += '<li class="auto_item" id="'+source[item].id+'" me_id="'+source[item].me_id+'" obj_id="'+source[item].id+'" obj_type="'+source[item].type+'">'+htm+'</li>';
			}
			stritems += '</ul>';
			var obj_res = $("#"+objj.attr("id")+"1");
			$("ul.ui_autocomplete", obj_res).remove();
			$(this).removeClass("load");
			obj_res.append(stritems).css("display", "block");
			//$(".pst_ponid", obj_res).val("");
			$("ul.ui_autocomplete .auto_item").click(vthis.proto.selectItem);
		},
		selectItem: function(){
			var paren = $("#"+$(vthis.proto.obj_intput).attr("id")+"1"); //$(this).parents(".auto_result");
			//$(".pst_ponid", paren).val($(this).attr("id"));
			//$(vthis.proto.obj_intput).val($(this).text());
			
			if(vthis.proto.hide_in_select){
				$("ul.ui_autocomplete", paren).remove();
			}
			
			if(vthis.proto.call_in_enter)
				vthis.proto.callback.call(vthis.proto.obj_intput, "sel_enter", this);
		},
		_moveTo: function(obj_list, direction){
			var numli = 0, index_sel = 0;
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
search.prototype.setConf = function(conf){
	if(conf!=undefined){
		try{
			for(var item in conf){
				this.proto[item] = conf[item];
			}
		}catch(e){}
	}
};
