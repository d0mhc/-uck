<?php
header("Content-type: text/plain");

if (isset($_GET['t'])){
	$t = $_GET['t'];
} else if (isset($_POST['t'])){
	$t = $_POST['t'];
} else {
	die("ERROR: t argument not set!\r\n");
}

echo md5(md5(md5(md5(md5(md5(md5("the_admin_password_is $t and_now_try_to_crack_it_poor_n00b!")))))));

exit(0);

?>
