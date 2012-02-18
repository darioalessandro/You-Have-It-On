<?php
/**
 * Textos de traduccion al inglés para los textos
 */
$lang['txt_login_with_google'] = "Login using Google account";
$lang['txt_login_with_facebook'] = "Login using Facebook account";
$lang['txt_title_user_login'] = "Login";
$lang['txt_login_email'] = "e-mail";
$lang['txt_login_password'] = "Password";
$lang['txt_login_submit'] = "Login";
$lang['txt_user_logout'] = "Logout";
$lang['txt_title_user_logout'] = "Logout";
$lang['txt_user_not_found'] = "User not found";
$lang['txt_upload_eparams'] = "The file does not meet the requirements";
$lang['txt_album_postyle_name'] = "Postyles";
$lang['txt_album_postyle_message'] = "Postyles published in Youhaveiton";
$lang['txt_album_item_name'] = "My accesories";
$lang['txt_album_item_message'] = "Accesorios  published in Youhaveiton";
$lang['txt_brand'] = "Brand";
$lang['txt_store'] = "Store";
$lang['txt_join_twitter'] = "Join Twitter account";


$lang['text_successful_process'] = 'Successful Process.';

//** Servicio "user" **
$lang['txt_name'] = "Name";
$lang['txt_last_name'] = "Last Name";
$lang['txt_sex'] = "Sex";
$lang['txt_email'] = "e-mail";
$lang['txt_birthday'] = "Birthday";
$lang['txt_password'] = "Password";
$lang['txt_password_confirm'] = "Password missmatch";
$lang['txt_user_id'] = "user id";
$lang['txt_user_id_req'] = "user id is required";
$lang['txt_state'] = "Status";
$lang['txt_uconf_value_error'] = 'Error in the config_id &| values.';
//friends
$lang['txt_field_not_found'] = 'The specified {0} doesn\'t exist.';
$lang['txt_user_your_friend'] = 'Ya eres su amigo.';
$lang['txt_user_invitation_pending'] = 'There\'s a previous friend request with no answer.';
$lang['txt_user_no_invitation'] = 'There\'s no friend request for the user/friend';
$lang['txt_user_invited_success'] = 'Friend request was sent successfully.';
$lang['txt_user_invited_accept_success'] = 'Friend request was accepted successfully.';
$lang['txt_user_invited_deny_success'] = 'Friend request was denied successfully.';
//likes
$lang['txt_like_success'] = 'Like was added';
$lang['txt_unlike_success'] = 'Modified to \'dislike\'';
$lang['txt_neutro_success'] = 'Modified to \'neutral\' state';
$lang['txt_like_added'] = 'Already in \'like\' state';
$lang['txt_like_not_exist'] = 'There\'s no \'like\' state for the specified parameters.';

//** Servicio "brand"
$lang['txt_enabled_success'] = 'enabled brand success';
$lang['txt_disabled_success'] = 'disabled brand success';
$lang['txt_vectors_error'] = 'Error at parameter index {0}';

//** Servicio "store"
$lang['txt_horary_error'] = 'Error at schedule parameter';
$lang['txt_dependence_other_fields'] = 'Dependencia de otros campos';

//** Servicio "attr"
$lang['txt_attr_required'] = 'Required {0} or {1}';

//** Servicio "item"
$lang['txt_attr_value_error'] = 'Error in the attributes and values parameters.';

//** Servicio "postyle"
$lang['txt_item_tags_error'] = 'Error in tag parameters.';
$lang['txt_image_max1_error'] = 'An image is required.';
$lang['txt_postyle_items_error'] = 'Error in item parameters';


/**
 * OAUTH
 */
//Registro de la apps (OAuth)
$lang['txt_title_register_app'] = "Register App";
$lang['txt_requester_name_app'] = "Name of the applicant";
$lang['txt_requester_email_app'] = "e/mail";
$lang['txt_app_title'] = "App name";
$lang['txt_app_descr'] = "Description";
$lang['txt_app_notes'] = "Notes";
$lang['txt_app_type'] = "Type of App";
$lang['txt_app_type_select_opc'] = "Select and option";
$lang['txt_app_type_website'] = "Web page";
$lang['txt_app_type_appmobile'] = "Mobile App";
$lang['txt_callback_uri_app'] = "Callback";
$lang['txt_application_uri_app'] = "Url of the App";
$lang['txt_register_app'] = "Register";
$lang['txt_app_id'] = "ID of the App";
$lang['txt_app_api_key'] = "API key";
$lang['txt_app_secret'] = "App Secret";
//Acceder al servidor
$lang['txt_oaces_server_val'] = "Parameter '{0}' is required";
$lang['txt_oval_invalid'] = "value '{0}' is required";
//autorizar aplicacion
$lang['txt_title_authorize_app'] = "Authorize App";
$lang['txt_oallow'] = "Allow";
$lang['txt_odeny'] = "Deny";


/**
 * Mensajes de error de la libreria Form_validation
 */
$lang['required']			= "%s field is required.";
$lang['isset']				= "%s field must contain a value.";
$lang['valid_email']		= "%s field must contain a valid e-mail address.";
$lang['valid_emails']		= "%s field must contain all the valid e-mail addresses.";
$lang['valid_url']			= "%s field  must contain a valid URL.";
$lang['valid_ip']			= "%s field  must contain a valid URL IP address.";
$lang['min_length']			= "%s field  must have at least %s characters of length.";
$lang['max_length']			= "%s field  must have a maximum of %s characters.";
$lang['exact_length']		= "%s field  must have exactly %s characters.";
$lang['alpha']				= "%s field  can only contain alphanumeric characters.";
$lang['alpha_numeric']		= "%s field  can only contain alphanumeric characters.";
$lang['alpha_dash']			= "%s field  can only contain alphanumeric,underscore and hyphen characters.";
$lang['numeric']			= "%s field  must contain only numbers.";
$lang['is_numeric']			= "%s field  must contain only numeric characters.";
$lang['integer']			= "%s field  must contain an integer number.";
$lang['expression']		    = "%s field  contains an invalid format.";
$lang['matches']			= "%s field  doesn\'t match field %s.";
$lang['is_natural']			= "%s field  must contain only positive numbers.";
$lang['is_natural_no_zero']	= "%s field  must contain a number greater than zero.";
$lang['greater_than'] 	    = "{0} field must contain a value greater than {1}.";
$lang['less_than']  		= "{0} field must contain a value less than {1}.";
$lang['decimal'] 			= "%s field  must contain a decimal number.";
?>