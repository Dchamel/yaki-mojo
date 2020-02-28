<?
	require_once "config.php";

	MYSQL_CONNECT($hostname,$username,$password) OR DIE("Не могу создать соединение ");
	@mysql_select_db("$dbName") or die("Не могу выбрать базу данных ");

    mysql_query("SET CHARACTER SET cp1251");
    mysql_query("SET NAMES cp1251");


	$dom = new DomDocument('1.0','UTF-8');
	//$dom = new DomDocument('1.0','windows-1251');
	$vigr = $dom->appendChild($dom->createElement('yakitoriya'));

	$menu = $vigr->appendChild($dom->createElement('menu'));


	$formo = mysql_query("select * FROM gshir_patslist where `al_show`=1 and `al_gde`<>1 and `al_id`!='34' order by `al_poz` ASC;");
	while($formob = mysql_fetch_array($formo)) {
		$idd= $formob['al_id'];
		$razdel = iconv("windows-1251","UTF-8",$formob['al_rusname']);
		$forelem = mysql_query("SELECT * FROM `gshir_pats` WHERE `pf_show`=1 and `al_id`=$idd and `pf_gde`<>1 order by `pf_poz`,`pf_id` ASC;");
		while($forel = mysql_fetch_array($forelem)) {
		$elem = $menu->appendChild($dom->createElement('element'));
		//id
		$rekv = $elem->appendChild($dom->createElement('id'));
		$rekv->appendChild($dom->createTextNode($forel['pf_id']));
		//признак ресторана
		$rekv = $elem->appendChild($dom->createElement('id_rest'));
		$rekv->appendChild($dom->createTextNode('1'));
		//признак кухни 0 японская 1 итальянская
		$rekv = $elem->appendChild($dom->createElement('pr_kuhni'));
		$rekv->appendChild($dom->createTextNode($formob['al_kuh']));
		//дата обновления
		$rekv = $elem->appendChild($dom->createElement('dataizm'));
		$rekv->appendChild($dom->createTextNode($forel['pf_dataizm']));
		//вес
		$rekv = $elem->appendChild($dom->createElement('ves'));
		$rekv->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8",$forel['pf_ves'])));
		//раздел
		//echo();
		//$razz = str_replace('&lt;p&gt;', '', $razdel);
		$rekv = $elem->appendChild($dom->createElement('reazdel'));
		$rekv->appendChild($dom->createTextNode($razdel));


		//Название блюда
		$rekv = $elem->appendChild($dom->createElement('name'));
		$rekv->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8",$forel['pf_rusname'])));
		//Описание блюда
		$innfo = $forel['pf_rusinfo'];
		//$innfo = str_replace(array("&lt;p&gt;","&amp;","&lt;/p&gt;","nbsp;","&#13","gt;","lt;"), '', $innfo);
		$innfo = html_entity_decode($innfo);
		$innfo = preg_replace("/&#?[a-z0-9]{2,8};/i","",$innfo);
		$innfo = preg_replace("'<[\/\!]*?[^<>]*?>'si","",$innfo);

		//$innfo = preg_replace("</p>","",$innfo);
		$rekv = $elem->appendChild($dom->createElement('info'));

		$rekv->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8",$innfo)));
		 //>> 7. Ссылка на фотографию перевью

		 $putf = 'http://'.$_SERVER['HTTP_HOST'].'/images/patslist/foto'.$forel['pf_id'].'.jpg';
		 $netf = false;
		 if(!file_exists('../images/patslist/foto'.$forel['pf_id'].'.jpg')){
		 $putf='';
		 $netf = true;
		 }
		 //$size = getimagesize($putf);
		 //if (!fopen($putf,"r")) $putf = '';

		 $rekv = $elem->appendChild($dom->createElement('img_prew'));
		 $rekv->appendChild($dom->createTextNode($putf));

		//>> 8. Ссылки на фотографии в большом размере
		$putf = 'http://'.$_SERVER['HTTP_HOST'].'/images/patslist/bfoto'.$forel['pf_id'].'.jpg';
		//$size = getimagesize($putf);
		//if (!fopen($putf,"r")) $putf = '';
		if($netf){
		 $putf='';
		 }

		$rekv = $elem->appendChild($dom->createElement('img_big'));
		$rekv->appendChild($dom->createTextNode($putf));


		 //>> 9. Признак доступно ли блюдо для доставки или доступно только в ресторане
		$rekv = $elem->appendChild($dom->createElement('dostavka'));
		$rekv->appendChild($dom->createTextNode('1'));
 		//>> 11. Стоимость блюда, без учета доставки
		$rekv = $elem->appendChild($dom->createElement('price_dostavka'));
		$rekv->appendChild($dom->createTextNode($forel['pf_rusprice']));
		//>> 10. новинки
		$rekv = $elem->appendChild($dom->createElement('new'));
		$rekv->appendChild($dom->createTextNode(0));
 		//>> 12. рекомендуем(популярные)
		$rekv = $elem->appendChild($dom->createElement('popular'));
		$rekv->appendChild($dom->createTextNode($forel['pf_izm']));
		// 13 упаковка включена
		$rekv = $elem->appendChild($dom->createElement('upak'));
		$rekv->appendChild($dom->createTextNode($forel['pf_upak']));

		}
	}

	//рестораны
	$restorans = $vigr->appendChild($dom->createElement('restorans'));
	$formo = mysql_query("SELECT * FROM `gshir_albom4` WHERE `al_show` = 1");
	$cher = '59.868499,30.315185';
	$nevs = '59.928173,30.372112';
	$ostr = '59.931807,30.334668';
	$pertn ='59.953796,30.330248';

	while($formob = mysql_fetch_array($formo)) {
		$idd= $formob['al_id'];
		$razdel = $formob['al_rusname'];
		$restoran = $restorans->appendChild($dom->createElement('restoran'));
		//id ресторана
		$rekv = $restoran->appendChild($dom->createElement('id_res'));
		$rekv->appendChild($dom->createTextNode('1'));
		//id
		$rekv = $restoran->appendChild($dom->createElement('id'));
		$rekv->appendChild($dom->createTextNode($formob['al_id']));
		//name

		$neme = $formob['al_rusname'];
		$rekv = $restoran->appendChild($dom->createElement('name'));
		$rekv->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8",$neme)));
		// info
		$innfo = $formob['al_rusinfo'];
		$innfo = html_entity_decode($innfo);
		//$innfo = preg_replace("/&#?[a-z0-9]{2,8};/i","",$innfo);
		//$innfo = preg_replace("/&#?[a-z0-9]{2,8};/i","",$innfo);
		$rar = explode("<p>", $innfo);
		$sxzala  ='';
		$metro  ='';
		$google  ='';
		$posmest = '';
		$ost     = '';

		foreach ($rar as $key => $value) {
			$smsxzala = stripos($value,'Схема зала');
			$smgoogl = stripos($value,  'Смотреть на карте Гугла');
			$etsbr = stripos($value,  '<br />');
			if($smsxzala !== false){
				//$sxzala =$value;
				$sxzalav = explode('"', $value);
				foreach ($sxzalav as $keys => $values) {
				if(stripos($values,  '/userfiles') !== false) {$sxzala =$values;}}

			}
			if ($smgoogl !== false){
				$smgooglv = explode('"', $value);
				foreach ($smgooglv as $keys => $values) {
				if(stripos($values,  'http') !== false) {$google =$values;}}
			}
			if($etsbr !== false){
				$rarur = explode("<br />", $value);
				foreach ($rarur as $keyu => $valueu) {
				$etsmetro = stripos($valueu,  "м.");
				if($etsmetro !== false) {$metro = $metro.$valueu;}
				$etsmest = stripos($valueu,  "мест");
				if($etsmest !== false) {$posmest = $valueu;}
				if(($etsmest === false) && ($etsmetro === false)){$ost.= $valueu;}

				}
			}
		}

		$sxzala  ='http://'.$_SERVER['HTTP_HOST'].$sxzala;
		$metro  =preg_replace("'<[\/\!]*?[^<>]*?>'si","",$metro);
		//$google  ='';
		$posmest = preg_replace("'<[\/\!]*?[^<>]*?>'si","",$posmest);
		$posmest_num = preg_replace("/[^\d]*/", "", $posmest);
		//echo $posmest;
		$ost     = preg_replace("'<[\/\!]*?[^<>]*?>'si","",$ost);
		$ost_telefon = stripos($ost, "тел.");
		$ost_bez_telefona = substr($ost,0 ,$ost_telefon);
		//echo $ost_bez_telefona."<br />";


		$ost_telefon = substr($ost, $ost_telefon+4);
		//echo $ost_telefon.$long1212."<br />";

		if (strpos($neme, 'Чернышевского')>0){$google = $cher;}
		if (strpos($neme, 'Невский')>0){$google = $nevs;}
		if (strpos($neme, 'Островского')>0){$google = $ostr;}
		if (strpos($neme, 'Петровская')>0){$google = $pertn;}
		//$google  = htmlspecialchars_decode($google);


		$rekv = $restoran->appendChild($dom->createElement('sxzala'));
		$rekv->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8",$sxzala)));

		$rekv = $restoran->appendChild($dom->createElement('metro'));
		$rekv->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8",$metro)));

		$rekv = $restoran->appendChild($dom->createElement('google'));
		$rekv->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8",$google)));

		$rekv = $restoran->appendChild($dom->createElement('posmest'));
		$rekv->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8",$posmest_num)));


		$rekv = $restoran->appendChild($dom->createElement('info'));
		$rekv->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8", $ost_bez_telefona)));

		$rekv = $restoran->appendChild($dom->createElement('phone'));
		$rekv->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8",$ost_telefon)));

		$fotores = $restoran->appendChild($dom->createElement('fotos_res'));

		$forelem = mysql_query("SELECT * FROM `gshir_portf4` where `al_id` = '$idd';");
		while($forel = mysql_fetch_array($forelem)) {

		$fotore = $fotores->appendChild($dom->createElement('foto_res'));
		//id
		$pfid = $forel['pf_id'];
		$putf = 'http://'.$_SERVER['HTTP_HOST'].'/images/portf4/foto'.$pfid.'.jpg';
		$rekv = $fotore->appendChild($dom->createElement('img_prew'));
		$rekv->appendChild($dom->createTextNode($putf));

		//id
		$putf = 'http://'.$_SERVER['HTTP_HOST'].'/images/portf4/foto'.$pfid.'b.jpg';
		$rekv = $fotore->appendChild($dom->createElement('img_big'));
		$rekv->appendChild($dom->createTextNode($putf));
		}
	}
	$actions = $vigr->appendChild($dom->createElement('actions'));
	$formo = mysql_query("SELECT * FROM `gshir_events` where `pg_show` =1");
	while($formob = mysql_fetch_array($formo)) {
		$action = $actions->appendChild($dom->createElement('action'));

		$rekv = $action->appendChild($dom->createElement('id'));
		$rekv->appendChild($dom->createTextNode($formob['pg_id']));

		$rekv = $action->appendChild($dom->createElement('field_of_work'));
		$rekv->appendChild($dom->createTextNode($formob['pg_rest']));

		$rekv = $action->appendChild($dom->createElement('name'));
		$rekv->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8",$formob['pg_rusname'])));
		$innfo =$formob['pg_rusinfo'];
		$innfo = html_entity_decode($innfo);
		$innfo = preg_replace("/&#?[a-z0-9]{2,8};/i","",$innfo);
		$innfo = preg_replace("'<[\/\!]*?[^<>]*?>'si","",$innfo);
		$rekv = $action->appendChild($dom->createElement('info'));
		$rekv->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8",$innfo)));

		$rekv = $action->appendChild($dom->createElement('data_nach'));
		$rekv->appendChild($dom->createTextNode($formob['pg_date']));

		$putfile = 'http://'.$_SERVER['HTTP_HOST'].'/images/events/foto'.$formob['pg_id'].'.jpg';
		$rekv = $action->appendChild($dom->createElement('imag'));
		$rekv->appendChild($dom->createTextNode($putfile));

		$putssilk = 'http://'.$_SERVER['HTTP_HOST'].'/stock/'.$formob['pg_url'];
		$rekv = $action->appendChild($dom->createElement('url'));
		$rekv->appendChild($dom->createTextNode($putssilk));



	}

	$news = $vigr->appendChild($dom->createElement('news'));
	$formo = mysql_query("SELECT * FROM `gshir_news` where `pg_show` =1 ORDER BY `pg_date` DESC LIMIT 10");
	while($formob = mysql_fetch_array($formo)) {
		$new = $news->appendChild($dom->createElement('new'));

		$rekv = $new->appendChild($dom->createElement('id'));
		$rekv->appendChild($dom->createTextNode($formob['pg_id']));

		$rekv = $new->appendChild($dom->createElement('name'));
		$rekv->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8",$formob['pg_rusname'])));
		$innfoff =$formob['pg_rusinfo'];
		$innfoff = html_entity_decode($innfoff);
		$innfoff = preg_replace("/&#?[a-z0-9]{2,8};/i","",$innfoff);
		$innfoff = preg_replace("'<[\/\!]*?[^<>]*?>'si","",$innfoff);
		$rekv = $new->appendChild($dom->createElement('info'));
		$rekv->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8",$innfoff)));

		$rekv = $new->appendChild($dom->createElement('data_nach'));
		$rekv->appendChild($dom->createTextNode($formob['pg_date']));

		$putfile = 'http://'.$_SERVER['HTTP_HOST'].'/images/news/foto'.$formob['pg_id'].'.jpg';
		$rekv = $new->appendChild($dom->createElement('imag'));
		$rekv->appendChild($dom->createTextNode($putfile));

		$putssilk = 'http://'.$_SERVER['HTTP_HOST'].'/news/'.$formob['pg_url'];
		$rekv = $new->appendChild($dom->createElement('url'));
		$rekv->appendChild($dom->createTextNode($putssilk));



	}

	//Доставка
	$dost = $vigr->appendChild($dom->createElement('dostavka'));
	$formo = mysql_query("SELECT * FROM `gshir_razdel` where `rz_url` ='usldostsushi'");
	while($formob = mysql_fetch_array($formo)) {
		$info = $dost->appendChild($dom->createElement('info'));

		$innfo =$formob['rz_rusinfo'];
		$innfo = html_entity_decode($innfo);
		$innfo = preg_replace("'<p[^>]*?>'si","<h2>",$innfo);
		$innfo = preg_replace("'</p[^>]*?>'si","</h2>",$innfo);
		$innfo = preg_replace("<li>","h3",$innfo);
		$innfo = preg_replace("'<br[^>]*?>'si","",$innfo);
		$innfo = preg_replace("'<span[^>]*?>'si","",$innfo);
		$innfo = preg_replace("'</span[^>]*?>'si","",$innfo);
		$innfo = preg_replace("'<br[^>]*?>'si","",$innfo);
		$innfo = preg_replace("'</wbr[^>]*?>'si","",$innfo);
		$innfo = preg_replace("'<wbr[^>]*?>'si","",$innfo);
		$innfo = preg_replace("'<strong[^>]*?>'si","",$innfo);
		$innfo = preg_replace("'</strong[^>]*?>'si","",$innfo);
		$innfo = preg_replace("'<ul[^>]*?>'si","",$innfo);
		$innfo = preg_replace("'</ul[^>]*?>'si","",$innfo);
		$innfo = preg_replace("'<b[^>]*?>'si","",$innfo);
		$innfo = preg_replace("'</b[^>]*?>'si","",$innfo);
		//$innfo = preg_replace("/<ul>;/i","",$innfo);
		//$innfo = preg_replace("<br />","",$innfo);
		$innfo = preg_replace("/&#?[a-z0-9]{2,8};/i","",$innfo);
		$innfo = substr($innfo,0,strlen($innfo) - 33);
		//$innfo = str_replace("'<h2> Последние новинки:</h2>'","",$innfo);
		//$innfo = preg_replace("'<[\/\!]*?[^<>]*'si","",$innfo);

		$info->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8",$innfo)));
	}


			@mysql_select_db("$dbNameMojo") or die("Не могу выбрать базу данных ");
			$formo = mysql_query("select * FROM gshir_patslist where `al_show`=1 and `al_gde`<>1 order by `al_poz` ASC;");
	while($formob = mysql_fetch_array($formo)) {
		$idd= $formob['al_id'];
		$razdel = iconv("windows-1251","UTF-8",$formob['al_rusname']);
		$forelem = mysql_query("SELECT * FROM `gshir_pats` WHERE `pf_show`=1 and `al_id`=$idd and `pf_gde`<>1 order by `pf_poz`,`pf_id` ASC;");
		while($forel = mysql_fetch_array($forelem)) {
		$elem = $menu->appendChild($dom->createElement('element'));
		//id
		$rekv = $elem->appendChild($dom->createElement('id'));
		$rekv->appendChild($dom->createTextNode($forel['pf_id']));
		//признак ресторана
		$rekv = $elem->appendChild($dom->createElement('id_rest'));
		$rekv->appendChild($dom->createTextNode('2'));
		//признак кухни 0 японская 1 итальянская
		$rekv = $elem->appendChild($dom->createElement('pr_kuhni'));
		$rekv->appendChild($dom->createTextNode('1'));
		//дата обновления
		$rekv = $elem->appendChild($dom->createElement('dataizm'));
		$rekv->appendChild($dom->createTextNode($forel['pf_dataizm']));
		//вес
		$rekv = $elem->appendChild($dom->createElement('ves'));
		$rekv->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8",$forel['pf_ves'])));
		//раздел
		//echo();
		//$razz = str_replace('&lt;p&gt;', '', $razdel);
		$rekv = $elem->appendChild($dom->createElement('reazdel'));
		$rekv->appendChild($dom->createTextNode($razdel));


		//Название блюда
		$rekv = $elem->appendChild($dom->createElement('name'));
		$rekv->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8",$forel['pf_rusname'])));
		//Описание блюда
		$innfo = $forel['pf_rusinfo'];
		//$innfo = str_replace(array("&lt;p&gt;","&amp;","&lt;/p&gt;","nbsp;","&#13","gt;","lt;"), '', $innfo);
		$innfo = html_entity_decode($innfo);
		$innfo = preg_replace("/&#?[a-z0-9]{2,8};/i","",$innfo);
		$innfo = preg_replace("'<[\/\!]*?[^<>]*?>'si","",$innfo);

		//$innfo = preg_replace("</p>","",$innfo);
		$rekv = $elem->appendChild($dom->createElement('info'));

		$rekv->appendChild($dom->createTextNode(iconv("windows-1251","UTF-8",$innfo)));
		 //>> 7. Ссылка на фотографию перевью

		 $putf = 'http://'.$_SERVER['HTTP_HOST'].'/images/patslist/foto'.$forel['pf_id'].'.jpg';
		 $netf = false;
		 //if(!file_exists('http://'.$_SERVER['HTTP_HOST'].'/images/patslist/foto'.$forel['pf_id'].'.jpg')){
			 if (!@fopen('http://'.$_SERVER['HTTP_HOST'].'/images/patslist/foto'.$forel['pf_id'].'.jpg', 'r')){
		 $putf='';
		 $netf = true;
		 }
		 //$size = getimagesize($putf);
		 //if (!fopen($putf,"r")) $putf = '';

		 $rekv = $elem->appendChild($dom->createElement('img_prew'));
		 $rekv->appendChild($dom->createTextNode($putf));

		//>> 8. Ссылки на фотографии в большом размере
		$putf = 'http://'.$_SERVER['HTTP_HOST'].'/images/patslist/bfoto'.$forel['pf_id'].'.jpg';
		//$size = getimagesize($putf);
		//if (!fopen($putf,"r")) $putf = '';
		if($netf){
		 $putf='';
		 }

		$rekv = $elem->appendChild($dom->createElement('img_big'));
		$rekv->appendChild($dom->createTextNode($putf));


		 //>> 9. Признак доступно ли блюдо для доставки или доступно только в ресторане
		$rekv = $elem->appendChild($dom->createElement('dostavka'));
		$rekv->appendChild($dom->createTextNode('1'));
 		//>> 11. Стоимость блюда, без учета доставки
		$rekv = $elem->appendChild($dom->createElement('price_dostavka'));
		$rekv->appendChild($dom->createTextNode($forel['pf_rusprice']));
		//>> 10. новинки
		$rekv = $elem->appendChild($dom->createElement('new'));
		$rekv->appendChild($dom->createTextNode(0));
 		//>> 12. рекомендуем(популярные)
		$rekv = $elem->appendChild($dom->createElement('popular'));
		$rekv->appendChild($dom->createTextNode($forel['pf_izm']));
		// 13 упаковка включена
		$rekv = $elem->appendChild($dom->createElement('upak'));
		$rekv->appendChild($dom->createTextNode('1'));

		}
	}
  
	$dom->formatOutput = true;
	$dom->save('formobil.xml');


?>
