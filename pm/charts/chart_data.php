<?php

/* http://teethgrinder.co.uk/open-flash-chart-2/ */
include_once($_SERVER["DOCUMENT_ROOT"] . 'public/charts/version-2-Jorm-2/php-ofc-library/open-flash-chart.php');

//$title = new title( "All Projects" );
//$title->set_style( "{font-size: 20px; font-family: Times New Roman; font-weight: bold; color: #A2ACBA; text-align: center;}" );

/* calculate each by counting open projects by the following:
Planning - status=planning
Critical -
Over Time-
On Time  -
*/

$d = array();
$d[] = new pie_value(100, "Planning");
$d[] = new pie_value(120, "Critical");
$d[] = new pie_value(99, "On Time");
$d[] = new pie_value(21, "Exceeded");

//
// override the PIE on-click event:
//
/*
$slice = new pie_value(100, "Planning");
$slice->on_click('http://example.com');
$d[] = $slice;
$slice = new pie_value(120, "Critical");
$slice->on_click('http://example.com');
$d[] = $slice;
$slice = new pie_value(99, "On Time");
$slice->on_click('http://example.com');
$d[] = $slice;
$slice = new pie_value(21, "Over Time");
$slice->on_click('http://example.com');
$d[] = $slice;
*/

$pie = new pie();
$pie->set_animate( true );
$pie->set_label_colour( '#432BAF' );
$pie->set_alpha( 0.75 );
$pie->set_tooltip( '#label# Projects #val#  (#percent#)' );
//
// default on-click event
//
$pie->on_click('pie_slice_clicked');
//
//
//
$pie->set_colours(
    array(
        '#07CC6D',    // income (green)
        '#77CC6D',    // income (green)
        '#FF5973',    // spend (pink)
        '#6D86CC'    // profit (blue)
    ) );

$pie->set_values( $d );

$chart = new open_flash_chart();
//$chart->set_title( $title );
$chart->add_element( $pie );

echo $chart->toPrettyString();

?>