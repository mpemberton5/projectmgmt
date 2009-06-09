<?php
/* $Id: cht_pso.php,v 1.1 2009/06/08 21:13:04 markp Exp $ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');
include_once(BASE.'database/database.php');

/* http://teethgrinder.co.uk/open-flash-chart-2/ */
include_once($_SERVER["DOCUMENT_ROOT"] . 'public/charts/version-2-Jorm-2/php-ofc-library/open-flash-chart.php');

//$title = new title( "Projects by Employee" );
//$title->set_style( "{font-size: 20px; font-family: Times New Roman; font-weight: bold; color: #000080; text-align: center;}" );

$result = mysql_query("select firstname,MedCtrLogin,count(Owner_ID) as kount from employees INNER JOIN projects on employees.employee_ID = projects.Owner_ID group by employees.employee_ID order by count(employee_ID) desc");

$data = array();
$max = 0;
while($row = mysql_fetch_array($result))
{
  $data[] = new pie_value(intval($row['kount']),$row['firstname'],$row['MedCtrLogin']);
}

/*
 TODO: debug code
print '<pre>';
print_r( $data );
print '</pre>';
*/

$pie = new pie();

$pie->alpha(0.5)
    ->add_animation( new pie_fade() )
    ->add_animation( new pie_bounce(5) )
    //->start_angle( 270 )
    ->start_angle( 0 )
    ->tooltip( '#val# of #total#' )
    ->colours(array('#FF3300','#3300CC','#003300','#660000','#FF368D','#996699'));

$pie->on_click('pie_slice_clicked');

$pie->set_values( $data );

$chart = new open_flash_chart();
$chart->set_bg_colour( '#FFFFFF' );
//$chart->set_title( $title );
$chart->add_element( $pie );


$chart->x_axis = null;

echo $chart->toPrettyString();

?>