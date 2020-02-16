<?

if ($_POST['days']) {

require_once "../../config.php";
MYSQL_CONNECT($hostname,$username,$password) OR DIE("Не могу создать соединение ");
@mysql_select_db("$dbName") or die("Не могу выбрать базу данных "); 
mysql_query("SET CHARACTER SET cp1251");
mysql_query("SET NAMES cp1251");

//подключение модуля графиков				
include("pChart/pData.class");  
include("pChart/pChart.class");

$myData = new pData();
				
$date_month_zapros = mysql_query("select `zz_date` from gshir_zakaz2 ORDER BY `zz_date`");
while ($itog2 = mysql_fetch_row($date_month_zapros)) {
	$arr[] = $itog2[0];
}
$itog3 = array_count_values ($arr);
$itog3 = array_slice ($itog3, -$_POST['days']);

foreach ($itog3 as $date => $quantity) {
	$myData->AddPoint($date,"date"); 
	$myData->AddPoint($quantity,"quantity"); 
	
}

$myData->SetAbsciseLabelSerie("date");
$myData->AddSerie("quantity");
$myData->SetSerieName(mb_convert_encoding("Количество заказов",'utf-8','windows-1251'), "quantity"); 
$graph = new pChart(1000,500); 
$graph->setFontProperties("../../../Fonts/tahoma.ttf",10); 
$graph->setGraphArea(85,30,950,400); 
$graph->drawFilledRoundedRectangle(7,7,993,493,5,240,240,240); 
$graph->drawRoundedRectangle(5,5,995,495,5,230,230,230); 
$graph->drawGraphArea(255,255,255,TRUE);
$graph->drawScale($myData->GetData(),$myData->GetDataDescription(),SCALE_NORMAL,150,150,150,true,0,2); 
$graph->drawGrid(4,TRUE,230,230,230,50); 
$graph->drawLineGraph($myData->GetData(),$myData->GetDataDescription()); 
$graph->drawPlotGraph($myData->GetData(),$myData->GetDataDescription(),3,2,255,255,255); 
$graph->setFontProperties("../../../Fonts/tahoma.ttf",10); 
$graph->drawLegend(90,35,$myData->GetDataDescription(),255,255,255); 
$graph->setFontProperties("../../../Fonts/tahoma.ttf",10); 
$graph->drawTitle(480,22,mb_convert_encoding("График",'utf-8','windows-1251'),50,50,50,-1,-1,true); 
$graph->Render("example1.png");

}
?>

<!DOCTYPE html> 
<html>
 <head>
  <meta charset="utf-8">
  <title>Пример страницы</title>
   </head>
 <body style="width: 1100px; margin: auto;">
  <form method="POST" action="zakaz_graph.php">
  <fieldset>
    <legend>Параметры построения графика:</legend>
    Количество дней назад:<br>
    <input type="text" name="days" value="30"><br><br>
    <input type="submit" value="Построить">
  </fieldset>
 </form>
 <div style="text-align: center;">
 <img src="example1.png"/>
 </div>
 </body>
</html>

<? 
	echo "<PRE>";
	print_r ($itog3);
	echo "</PRE>";
?>
