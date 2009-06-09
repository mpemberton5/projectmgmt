<H2>Error Page</H2>
<?php
echo $_REQUEST['error'];

echo "<br />DATA RECEIVED: <br />";
echo "<pre>";
print_r($_POST);
print_r($_GET);
print_r($_REQUEST);
echo "</pre>";

?>