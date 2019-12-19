<?
// header('Content-type: text/html; charset=utf-8');

require_once ("../config.php");
require_once ('PHPExcel.php');
require_once ('PHPExcel/IOFactory.php');
require_once ("../func/photo.php"); //  фото-функции	

MYSQL_CONNECT($hostname,$username,$password) OR DIE("Не могу создать соединение ");
@mysql_select_db("$dbName") or die("Не могу выбрать базу данных "); 
mysql_query("SET CHARACTER SET cp1251");
mysql_query("SET NAMES cp1251");


$xls = PHPExcel_IOFactory::load('mass_checkndo.xls');
$xls->setActiveSheetIndex(0);
$sheet = $xls->getActiveSheet();

// Обрабатываем и подготавливаем содержимое Икселя 

echo '<table border="1">';
$counter = 0;

for ($i = 2; $i <= 220; $i++) {  // С какой по какую строку

    // $red_id //ИД блюда
    
    //Запись самих данных 
    $rusname = iconv("utf-8","windows-1251",$sheet->getCellByColumnAndRow(0, $i)->getValue());//Имя
    $ves = iconv("utf-8","windows-1251",$sheet->getCellByColumnAndRow(1, $i)->getValue());//Вес
    $rusprice = iconv("utf-8","windows-1251",$sheet->getCellByColumnAndRow(2, $i)->getValue());//Цена
    $rusinfo = iconv("utf-8","windows-1251",$sheet->getCellByColumnAndRow(3, $i)->getValue());//Состав
    
    $showw = 1; //Показывать 0 или 1
    $al_id = 143;  //ИД категории 139 в данном случае
    // $rusname=$ves;

        echo '<tr>';
      
        // Столбцы
        echo '<td>'. $i .'</td>';
        echo '<td>'. $rusname .'</td>';
        
        //Поиск соответствия в БД и вывод
        echo '<td>';
        $zaoo = mysql_query("select * from gshir_pats WHERE pf_rusname='$rusname';");
        $raoo = mysql_fetch_array($zaoo);
            if ($raoo!=NULL) {
                $counter++;
                // $se = array("Суси", "Гункан");
                // $newphrase = str_replace($se,'', $rusname);
                // $zaoo2 = mysql_query("select * from gshir_pats WHERE pf_rusname LIKE lower('%$newphrase%');");
                // $raoo2 = mysql_fetch_array($zaoo2);
                // printf($raoo2['pf_rusname']);
            }
        printf($raoo['pf_rusname']);
        echo '<br />';
        echo '</td>';
        

        echo '</tr>';

    
    //Запись в категорию всех товаров из Икселя
    // $query = "INSERT INTO gshir_pats SET `pf_rusname`='$rusname',`pf_rusinfo`='$rusinfo',`pf_show`='$showw',`al_id`='$al_id',`pf_rusprice`='$rusprice',`pf_ves`='$ves',`pf_dataizm`=NOW();";
    
    //Удаление из категории всех товаров
    // $query ="DELETE FROM gshir_pats WHERE `al_id`='$al_id'";
    
    // $result = mysql_query($query);
    // $t_id=mysql_insert_id();
    // echo($t_id);
    
    // Загрузка фото
    // Если есть допфото 1 
    // TransImg($_FILES["photo"]["tmp_name"],175,175,"../images/patslist/foto$t_id.jpg");
    // Если есть фото-ОСНОВНОЕ 
    // TransImg($_FILES["photo2"]["tmp_name"],800,800,"../images/patslist/bfoto$t_id.jpg");
    
    }

    echo '</table>';
    echo $counter;


    // Показываем содержимое всего
    echo "<p>----------- Содержимое загруженного и отображаемого. Взято из БД сайта. -----------</p>";
    $zaoo = mysql_query("select * from gshir_pats WHERE pf_show='1';");
    

    echo '<table border="1">';
    $counter=0;
     while($raoo = mysql_fetch_array($zaoo))
     {
       $counter++;
       
       $russname = $raoo['pf_rusname'];
       $cat_num = $raoo['al_id'];
       
    //    echo($russname);
    //    echo("<br/>");	
       
       echo '<tr>';
      
       // Столбцы
       echo '<td>'. $counter .'</td>';
       echo '<td>'. $russname .'</td>';
       echo '<td>'. $cat_num .'</td>';
       $cat_name_arr = mysql_fetch_array(mysql_query("select * from gshir_patslist WHERE al_id='$cat_num';"));
       $cat_name = $cat_name_arr['al_rusname'];
       echo '<td>'. $cat_name .'</td>';

       echo '</tr>';

    }	
    
    echo '</table>';
    