<?php
/*
 * iniciar OAuth store
 */
require_once APPPATH.'/libraries/oauth/OAuthStore.php';
require_once APPPATH.'/libraries/oauth/OAuthServer.php';
include_once APPPATH.'/libraries/oauth/OAuthException2.php';
include_once APPPATH.'/libraries/oauth/OAuthRequester.php';
$config['db_oauth'] = array("server" => "localhost",
				"username" => "recurso1_youhave",
				"password" => "XM;m!77JP8PL",
				"database" => "recurso1_youhave"
			);
$config['store_oauth'] = OAuthStore::instance('MySQL', $config['db_oauth']);
$config['server_oauth'] = new OAuthServer();

$config['oa_request_token'] = 'services/oauth/server?action=request_token';
$config['oa_authorize'] = 'services/oauth/server?action=authorize';
$config['oa_access_token'] = 'services/oauth/server?action=access_token';
$config['oa_server'] = 'services/oauth/server';
?>