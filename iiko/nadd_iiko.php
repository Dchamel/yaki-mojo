<?php
require_once ("../../mod/patslist/config.php"); // category config
require_once ("../../func/photo.php"); //  foto functions	
require_once ("../../func/all_func.php");	// all other functions
require_once ("../../config.php"); // config

// 2 Translit Functions
// Translit Array
function rus2translit($string) {
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );
    return strtr($string, $converter);
}
// Main Translit Func
function str2url($str) {
    // to translit
    $str = rus2translit($str);
    // to lowercase
    $str = strtolower($str);
    // convert all other things to "-"
    $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
    // del first and last '-'
    $str = trim($str, "-");
    return $str;
}

MYSQL_CONNECT($hostname,$username,$password) OR DIE("Не могу создать соединение ");
@mysql_select_db("$dbName") or die("Не могу выбрать базу данных "); 
mysql_query("SET CHARACTER SET utf8");
mysql_query("SET NAMES utf8"); 

header('Content-type: text/html; charset=UTF-8');

// Test IIKO Acc Connection Data
$server = "iiko.biz";
$port = "";  // It can be empty
$userId = ""; //iiko restaurant ID 
$userSecret = ""; // Password
$date = date("Y-m-d");

// Request for an access token
$url = "https://$server:$port/api/0/auth/access_token";
$params = array( 
	'user_id'     => $userId,
	'user_secret' => $userSecret
 );
$data = curlGet($url, $params);
$accessToken = trim($data, '"');

echo $accessToken;

// Get organisations list
$url = "https://$server:$port/api/0/organization/list";
    $params = array(
      'access_token' => $accessToken
    );
    $json = curlGet($url, $params);
	$orgList = json_decode($json, true);
echo "<PRE>";
print_r ($orgList);

// Get ID of First Organisation in List
$orgGuid = $orgList[0]['id'];
print_r ($orgGuid);

// Get delivery list of this organisation for Today
$url = "https://$server:$port/api/0/orders/deliveryOrders";
$params = array(
  'access_token' => $accessToken, 
  'organization' => $orgGuid,
  'dateFrom' => $date,
  'dateTo' => $date,
  'request_timeout' => '00:02:00'
);
$json = curlGet($url, $params);
$deliveryOrders = json_decode($json, true);
echo 'Всего заказов за день ' . $date . ': ' . count($deliveryOrders) . '<br>';

// Get whole menu
$url = "https://$server:$port/api/0/nomenclature/$orgGuid";
$params = array(
  'access_token' => $accessToken,
);
$json = curlGet($url, $params);
$menu = json_decode($json, true);
$prod_arr = $menu['products'];

// Write all to the DB
foreach ($menu['groups'] as $group) {
    
	// Translit
	$url_cat = str2url($group['name']);
  
	$cat_name = $group['name'];
	$cat_id = $group['id'];

	// If cat Exists already - Update, NO - Create
	$query_check_cat = mysql_query("SELECT al_id FROM gshir_patslist WHERE `iiko_id`='$cat_id';");
	$al_id = mysql_fetch_array($query_check_cat);
	$al_id = $al_id['al_id'];
	print_r ('Current Category: '.$al_id);
	echo "<br/>";
		
	if ($al_id !== NULL) {
		$query = mysql_query("UPDATE gshir_patslist SET `al_rusname`='$cat_name', `al_ruskey`='$cat_name', `al_rustit`='$cat_name', `al_rusdes`='$cat_name', `al_url`='$url_cat' WHERE `iiko_id`='$cat_id' LIMIT 1;");
		write_prod ($cat_id,$al_id,$prod_arr);
	}
	else {
		$query = mysql_query("INSERT INTO gshir_patslist VALUES('','$cat_name','','50','1','$cat_name','$cat_name','$cat_name','','$url_cat','','','','','','','','','','','','','','$cat_id')");
		$query_check_cat = mysql_query("SELECT al_id FROM gshir_patslist WHERE `iiko_id`='$cat_id';");
		$al_id = mysql_fetch_array($query_check_cat);
		$al_id = $al_id['al_id'];
		write_prod ($cat_id,$al_id,$prod_arr);
	}
	
}

// Write Items of the Category to the DB
function write_prod($cat_id,$al_id,$prod_arr) {

	print_r ('Category ID from Function: '.$cat_id);
	echo "<br/>";
	print_r ('AL ID from Funct: '.$al_id);
	echo "<br/>";
	echo "<br/>";
	
	echo "<PRE>";
	// print_r ($prod_arr);

foreach ($prod_arr as $product) {
	
		$prod_id_parent = $product['parentGroup'];
		
		$prod_name = $product['name'];
		$prod_descr = $product['description']; 
		$prod_price = $product['price'];
		$prod_weight = $product['weight'];
		$prod_id = $product['id'];
		
		// $al_id
		
		// If Item belongs to the Category via ID - Write
		if ($prod_id_parent == $cat_id) {
			$prod_img = $product['images'][0]['imageUrl'];
			print_r ($prod_img);
			// If Item already exists - Update, Another - Create
			$query_check_prod = mysql_query("SELECT pf_id FROM gshir_pats WHERE `iiko_id`='$prod_id';");
			$pf_id = mysql_fetch_array($query_check_prod);
			$pf_id = $pf_id['pf_id'];
				echo $pf_id;		
			if ($pf_id !== NULL) {
				$query = mysql_query("UPDATE gshir_pats SET `pf_rusname`='$prod_name',`pf_rusinfo`='$prod_descr',`pf_rusprice`='$prod_price',`pf_engprice`='$prod_price',`pf_ves`='$prod_weight', pf_dataizm =NOW() WHERE `iiko_id`='$prod_id' LIMIT 1;");
				// Foto Function, Resize - Small Image (Thumbnail)
				TransImg($prod_img,132,132,"../../../images/patslist/foto$pf_id.jpg");
				// Foto Function, Resize - Big Image 
				TransImg($prod_img,457,457,"../../../images/patslist/bfoto$pf_id.jpg");
			}
			else {
				$result = mysql_query("INSERT INTO gshir_pats VALUES('','$prod_name','$prod_descr','50','1','$al_id','','','','','','$prod_price','','','','','','$prod_price','','','','','','1','0','0','$prod_weight','','0',NOW(),'0','$prod_id','$prod_id_parent')");
				// фото малое
				TransImg($prod_img,132,132,"../../../images/patslist/foto$pf_id.jpg");
				// фото большое
				TransImg($prod_img,457,457,"../../../images/patslist/bfoto$pf_id.jpg");
			}			
		}
}
}

// echo "<PRE>";
// print_r ($menu);


function curlGet($url, array $get = null) 
  {
    $options = array(
      CURLOPT_URL => $url . (strpos($url, "?") === false ? "?" : "") . http_build_query($get),
      CURLOPT_HEADER => 0,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYHOST => 0,
      CURLOPT_SSL_VERIFYPEER => 0,
    );
    
    // echo 'curlGet: <a href="' . $options[CURLOPT_URL] . '">' . $options[CURLOPT_URL] . '</a><br>';
    
    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
  }							

?>
