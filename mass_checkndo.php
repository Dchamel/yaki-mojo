<?
// header('Content-type: text/html; charset=utf-8');

require_once ("../config.php");
require_once ('PHPExcel.php');
require_once ('PHPExcel/IOFactory.php');
require_once ("../func/photo.php"); //  ����-�������	

MYSQL_CONNECT($hostname,$username,$password) OR DIE("�� ���� ������� ���������� ");
@mysql_select_db("$dbName") or die("�� ���� ������� ���� ������ "); 
mysql_query("SET CHARACTER SET cp1251");
mysql_query("SET NAMES cp1251");


$xls = PHPExcel_IOFactory::load('mass_checkndo.xls');
$xls->setActiveSheetIndex(0);
$sheet = $xls->getActiveSheet();

// ������������ � �������������� ���������� ������ 

echo '<table border="1">';
$counter = 0;

for ($i = 2; $i <= 220; $i++) {  // � ����� �� ����� ������

    // $red_id //�� �����
    
    //������ ����� ������ 
    $rusname = iconv("utf-8","windows-1251",$sheet->getCellByColumnAndRow(0, $i)->getValue());//���
    $ves = iconv("utf-8","windows-1251",$sheet->getCellByColumnAndRow(1, $i)->getValue());//���
    $rusprice = iconv("utf-8","windows-1251",$sheet->getCellByColumnAndRow(2, $i)->getValue());//����
    $rusinfo = iconv("utf-8","windows-1251",$sheet->getCellByColumnAndRow(3, $i)->getValue());//������
    
    $showw = 1; //���������� 0 ��� 1
    $al_id = 143;  //�� ��������� 139 � ������ ������
    // $rusname=$ves;

        echo '<tr>';
      
        // �������
        echo '<td>'. $i .'</td>';
        echo '<td>'. $rusname .'</td>';
        
        //����� ������������ � �� � �����
        echo '<td>';
        $zaoo = mysql_query("select * from gshir_pats WHERE pf_rusname='$rusname';");
        $raoo = mysql_fetch_array($zaoo);
            if ($raoo!=NULL) {
                $counter++;
                // $se = array("����", "������");
                // $newphrase = str_replace($se,'', $rusname);
                // $zaoo2 = mysql_query("select * from gshir_pats WHERE pf_rusname LIKE lower('%$newphrase%');");
                // $raoo2 = mysql_fetch_array($zaoo2);
                // printf($raoo2['pf_rusname']);
            }
        printf($raoo['pf_rusname']);
        echo '<br />';
        echo '</td>';
        

        echo '</tr>';

    
    //������ � ��������� ���� ������� �� ������
    // $query = "INSERT INTO gshir_pats SET `pf_rusname`='$rusname',`pf_rusinfo`='$rusinfo',`pf_show`='$showw',`al_id`='$al_id',`pf_rusprice`='$rusprice',`pf_ves`='$ves',`pf_dataizm`=NOW();";
    
    //�������� �� ��������� ���� �������
    // $query ="DELETE FROM gshir_pats WHERE `al_id`='$al_id'";
    
    // $result = mysql_query($query);
    // $t_id=mysql_insert_id();
    // echo($t_id);
    
    // �������� ����
    // ���� ���� ������� 1 
    // TransImg($_FILES["photo"]["tmp_name"],175,175,"../images/patslist/foto$t_id.jpg");
    // ���� ���� ����-�������� 
    // TransImg($_FILES["photo2"]["tmp_name"],800,800,"../images/patslist/bfoto$t_id.jpg");
    
    }

    echo '</table>';
    echo $counter;


    // ���������� ���������� �����
    echo "<p>----------- ���������� ������������ � �������������. ����� �� �� �����. -----------</p>";
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
      
       // �������
       echo '<td>'. $counter .'</td>';
       echo '<td>'. $russname .'</td>';
       echo '<td>'. $cat_num .'</td>';
       $cat_name_arr = mysql_fetch_array(mysql_query("select * from gshir_patslist WHERE al_id='$cat_num';"));
       $cat_name = $cat_name_arr['al_rusname'];
       echo '<td>'. $cat_name .'</td>';

       echo '</tr>';

    }	
    
    echo '</table>';
    