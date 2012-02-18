<?php
/**
 * Textos de traduccion al ingles para las Excepciones
 */
$lang['txt_user_not_found'] = "You {0} must submit an {1} email address";
$lang['txt_login_with_google'] = "Ingles Iniciar sesión con google";
$lang['txt_login_with_facebook'] = "Ingles Iniciar sesión con facebook";
$lang['txt_title_user_login'] = "Inicio de sesión del usuario";
$lang['txt_login_email'] = "Correo electrónico";
$lang['txt_login_password'] = "Contraseña";
$lang['txt_login_submit'] = "Acceder";
$lang['txt_user_logout'] = "Cierre de sesión";
$lang['txt_title_user_logout'] = "Cierre de sesión";
$lang['txt_user_not_found'] = "Usuario no encontrado";
$lang['txt_upload_eparams'] = "El archivo no cumple con los requisitos";
$lang['txt_album_postyle_name'] = "Postyles";
$lang['txt_album_postyle_message'] = "Postyles publicados en LML";
$lang['txt_album_item_name'] = "Mis accesorios";
$lang['txt_album_item_message'] = "Accesorios publicados en LML";
$lang['txt_brand'] = "Marca";
$lang['txt_store'] = "Tienda";
$lang['txt_join_twitter'] = "Unir mi cuenta de twitter";


$lang['text_successful_process'] = 'ingles Proceso exitoso.';

//** Servicio "user" **
$lang['txt_name'] = "Nombre";
$lang['txt_last_name'] = "Apellido";
$lang['txt_sex'] = "Sexo";
$lang['txt_email'] = "Correo electrónico";
$lang['txt_birthday'] = "Cumpleaños";
$lang['txt_password'] = "Contraseña";
$lang['txt_user_id'] = "Id del usuario";
$lang['txt_user_id_req'] = "Id del usuario es requerido";
$lang['txt_state'] = "Estado";
$lang['txt_uconf_value_error'] = 'Error en los parámetros de config_id y/o values';
//friends
$lang['txt_field_not_found'] = 'El {0} especificado no existe.';
$lang['txt_user_your_friend'] = 'Ya eres su amigo.';
$lang['txt_user_invitation_pending'] = 'Ya hay una invitación que esta pendiente.';
$lang['txt_user_no_invitation'] = 'No existe invitación para el usuario y el amigo';
$lang['txt_user_invited_success'] = 'Se envió la invitación correctamente.';
$lang['txt_user_invited_accept_success'] = 'Se acepto la invitación correctamente.';
$lang['txt_user_invited_deny_success'] = 'Se denegó la invitación correctamente.';
//likes
$lang['txt_like_success'] = 'Se agrego el me gusta correctamente';
$lang['txt_unlike_success'] = 'Se modifico a no me gusta correctamente';
$lang['txt_neutro_success'] = 'Se modifico a neutro correctamente';
$lang['txt_like_added'] = 'Ya esta agregado el me gusta';
$lang['txt_like_not_exist'] = 'No existe un me gusta para los parámetros especificados';

//** Servicio "brand"
$lang['txt_enabled_success'] = 'Se activo con éxito';
$lang['txt_disabled_success'] = 'Se desactivo con éxito';
$lang['txt_vectors_error'] = 'Error en el parámetro {0}';

//** Servicio "store"
$lang['txt_horary_error'] = 'Error en los parámetros de horario';
$lang['txt_dependence_other_fields'] = 'Dependencia de otros campos';

//** Servicio "attr"
$lang['txt_attr_required'] = 'Es requerido {0} o {1}';

//** Servicio "item"
$lang['txt_attr_value_error'] = 'Error en los parámetros de atributos y valores';

//** Servicio "postyle"
$lang['txt_item_tags_error'] = 'Error en los parámetros de tags';
$lang['txt_image_max1_error'] = 'Una imagen es requerida';
$lang['txt_postyle_items_error'] = 'Error en los parámetros de items';


/**
 * OAUTH
 */
//Registro de la apps (OAuth)
$lang['txt_title_register_app'] = "Registrar aplicaciones";
$lang['txt_requester_name_app'] = "Nombre del solicitante";
$lang['txt_requester_email_app'] = "Correo electrónico";
$lang['txt_app_title'] = "Titulo de la aplicaciónl";
$lang['txt_app_descr'] = "Descripción de la aplicación";
$lang['txt_app_notes'] = "Notas";
$lang['txt_app_type'] = "Tipo de la aplicación";
$lang['txt_app_type_select_opc'] = "Seleccionar una opción";
$lang['txt_app_type_website'] = "Página web";
$lang['txt_app_type_appmobile'] = "Aplicación móvil";
$lang['txt_callback_uri_app'] = "Devolución de llamada";
$lang['txt_application_uri_app'] = "Url de la aplicación";
$lang['txt_register_app'] = "Registrar";
$lang['txt_app_id'] = "ID de la aplicación";
$lang['txt_app_api_key'] = "Clave de la API";
$lang['txt_app_secret'] = "Aplicación secreto";
//Acceder al servidor
$lang['txt_oaces_server_val'] = "El parámetro '{0}' es requerido";
$lang['txt_oval_invalid'] = "El valor de '{0}' es invalido";
//autorizar aplicacion
$lang['txt_title_authorize_app'] = "Autorizar aplicación";
$lang['txt_oallow'] = "Permitir";
$lang['txt_odeny'] = "Negar";


/**
 * Mensajes de error de la libreria Form_validation
 */
$lang['required']			= "The %s field is required.";
$lang['isset']				= "The %s field must have a value.";
$lang['valid_email']		= "The %s field must contain a valid email address.";
$lang['valid_emails']		= "The %s field must contain all valid email addresses.";
$lang['valid_url']			= "The %s field must contain a valid URL.";
$lang['valid_ip']			= "The %s field must contain a valid IP.";
$lang['min_length']			= "The %s field must be at least %s characters in length.";
$lang['max_length']			= "The %s field can not exceed %s characters in length.";
$lang['exact_length']		= "The %s field must be exactly %s characters in length.";
$lang['alpha']				= "The %s field may only contain alphabetical characters.";
$lang['alpha_numeric']		= "The %s field may only contain alpha-numeric characters.";
$lang['alpha_dash']			= "The %s field may only contain alpha-numeric characters, underscores, and dashes.";
$lang['numeric']			= "The %s field must contain only numbers.";
$lang['is_numeric']			= "The %s field must contain only numeric characters.";
$lang['integer']			= "The %s field must contain an integer.";
$lang['expression']         = "The %s field is not in the correct format.";
$lang['matches']			= "The %s field does not match the %s field.";
$lang['is_natural']			= "The %s field must contain only positive numbers.";
$lang['is_natural_no_zero']	= "The %s field must contain a number greater than zero.";
$lang['greater_than'] 	    = "El campo {0} debe tener un valor mayor que {1}.";
$lang['less_than']  		= "El campo {0} debe tener un valor menor que {1}.";
$lang['decimal'] 			= "El campo %s debe ser un número decimal.";
?>