<?php
/* $Id: cht_pbs.php,v 1.1 2009/06/08 21:13:04 markp Exp $ */

//security check
if (!isset($_SESSION['UID'])) {
	die('Direct file access not permitted');
}

require_once('path.php');
require_once(BASE.'includes/security.php');
include_once(BASE.'database/database.php');

/* http://teethgrinder.co.uk/open-flash-chart-2/ */
include_once($_SERVER["DOCUMENT_ROOT"] . 'public/charts/version-2-Jorm-2/php-ofc-library/open-flash-chart.php');

$result = mysql_query("select count(*) as kount from projects where EndDate < DATE_ADD(now(),INTERVAL 7 DAY) and PercentComplete<75");
$result1 = mysql_query("select count(*) as tkount from projects");


$data = array();

$data[] = new pie_value(6, "Critical");
$data[] = new pie_value(29, "In Progress");

while($row = mysql_fetch_array($result))
{
  $critnum = intval($row['kount']);
}

while($row1 = mysql_fetch_array($result1))
{
  $totnum = intval($row1['tkount']);
}

$data = array();

$data[] = new pie_value(6, "Critical");
////$data[] = new pie_value(" $critnum ", "Critical");
$data[] = new pie_value(29, "In Progress");
////1$data[] = new pie_value(" $totnum ", "In Progress");



//$title = new title( 'Project Status Overview' );
//$title->set_style( "{font-size: 20px; font-family: Times New Roman; font-weight: bold; color: #000080; text-align: center;}" );

$pie = new pie();
$pie->alpha(0.5)
    ->add_animation( new pie_fade() )
    ->add_animation( new pie_bounce(5) )
    //->start_angle( 270 )
    ->start_angle( 0 )
    ->tooltip( '#val# of #total#' )
    ->colours(array('#8A2BE2','#8B008B'));

//$pie->set_values( array(new pie_value(" $critnum ", "Critical"),new pie_value(" $totnum ", "In Progress")) );
//$pie->set_values( array(new pie_value(6, "Critical"),new pie_value(29, "In Progress")) );
$pie->set_values( $data );



$chart = new open_flash_chart();
$chart->set_bg_colour( '#FFFFFF' );
//$chart->set_title( $title );
$chart->add_element( $pie );


$chart->x_axis = null;

echo $chart->toPrettyString();
