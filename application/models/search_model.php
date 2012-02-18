<?php
class search_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('image_model');
	}
	
	
	
	/**
	 * Obtiene una lista de stores, las cuales coinciden con los filtros espesificados
	 * @param array $params. Contiene los parametros que se enviaron por post y get
	 */
	public function search($params){
		$sql_items = '';
		if(isset($params['token_user_id'])){
			$sql_items = "(SELECT i.id, i.label, IFNULL(b.name, '') AS brand, IFNULL(i.price, '') AS price, 
				IFNULL(sl.name, '') AS store, '' AS lat, '' AS lon, 'item' AS type FROM items AS i 
				LEFT JOIN brands AS b ON b.id = i.brand_id LEFT JOIN store_locations AS sl ON 
				sl.id = i.bought_in INNER JOIN items_users AS iu ON i.id = iu.item_id 
				WHERE LOWER(i.label) LIKE LOWER('".$params['search_text']."%') AND 
					item_valid( ".$params["token_user_id"].", iu.user_id, i.id )=1) 
				UNION
				(SELECT id, CONCAT( name,  ' ', last_name ) AS label,  '' AS brand,  '' AS price,  '' AS store,  '' AS lat,  '' AS lon,  'user' AS type 
				FROM users WHERE CONCAT( name,  ' ', last_name ) LIKE  '%".$params['search_text']."%'
					OR email LIKE  '%".$params['search_text']."%')
				UNION";
		}else
			$params['token_user_id'] = '';
		$query = Sys::pagination("
			SELECT id, label, brand, price, store, lat, lon, type, '".$params['token_user_id']."' AS me_id
			FROM (".$sql_items." 
				(SELECT sl.id, sl.name AS label, '' AS brand, '' AS price, '' AS store, l.lat, l.lon, 
				'store' AS type FROM store_locations AS sl LEFT JOIN locations AS l ON l.id = sl.location_id 
				WHERE LOWER(sl.name) LIKE LOWER('".$params['search_text']."%'))
					UNION 
				(SELECT b.id, b.name AS label, '' AS brand, '' AS price, '' AS store, '' AS lat, '' AS lon, 
				'brand' AS type FROM brands AS b  WHERE LOWER(b.name) LIKE LOWER('".$params['search_text']."%'))) AS t", $params);
		
		$stores = array('results' => array());
		
		$res_store = $this->db->query($query);
		if($res_store->num_rows() > 0){			
			foreach($res_store->result_array() as $row){
				$row['address'] = '';
				//obtenemos la direccion del servicio de google
				if($row['type'] == 'store'){
					$this->load->model("store_model");
					$para = array(
						'result_items_per_page' => '1',
						'result_page' => '1',
						'filter_store_location_id' => $row['id']
					);
					$resul = $this->store_model->getStores($para, false);
					$resul = $resul['stores'][0];
					if($resul['formatted_address'] != '')
						$row['address'] = $resul['formatted_address'];
					else
						$row['address'] = $resul['street_name'].', '.$resul['city'].', '.$resul['state'].', '.$resul['country'];
					/*$dir_google = (array)json_decode(file_get_contents("http://maps.google.com/maps/api/geocode/json?latlng=".$row['lat'].",".$row['lon']."&sensor=false"));
					
					if(count($dir_google['results']) > 0){
						$dir_google['results'][0] = (array)$dir_google['results'][0];
						$row['address'] = $dir_google['results'][0]['formatted_address'];
					}*/
				}
				unset($row['lat'], $row['lon']);
				
				
				//se obtienen la imagen
				$table = ''; $filed = '';
				switch($row['type']){
					case 'item': $table = 'items_imgs'; $filed = 'item_id'; break;
					case 'store': $table = 'store_locations_imgs'; $filed = 'store_location_id'; break;
					case 'brand': $table = 'brands_imgs'; $filed = 'brand_id'; break;
					case 'user': $table = 'users_imgs'; $filed = 'user_id'; break;
				}
				$row['images'] = array();
				if($table != ''){
					$res_imgs = $this->db->query("SELECT bi.id, i.file_name, i.file_type, i.file_size, bi.is_primary 
						FROM ".$table." AS bi INNER JOIN images AS i ON i.id = bi.image_id 
						WHERE bi.".$filed." = ".$row['id']." AND bi.enable = 1 LIMIT 1");
					if($res_imgs->num_rows() > 0){
						$imgs = $res_imgs->row_array();
						$sizes = array(
							array(
								'url' => UploadFile::urlBig().$imgs['file_name'],
								'size' => 'B'
							),
							array(
								'url' => UploadFile::urlMedium().$imgs['file_name'],
								'size' => 'M'
							),
							array(
								'url' => UploadFile::urlSmall().$imgs['file_name'],
								'size' => 'SN'
							),
							array(
								'url' => UploadFile::urlSmallSquare().$imgs['file_name'],
								'size' => 'SS'
							)
						);
						$row['images'] = $sizes;
					}
				}
					
					
				$stores['results'][] = $row;
			}
		}
		return $stores;
	}
	
}
?>