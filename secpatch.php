<?php
header("Content-type: text/plain");

$ht = fopen(".htaccess", "a+");

if (!$ht){
	die("Error: Cannot open .htaccess file!\r\n");
}

$patch = <<<EOF

<Files ~ "\.xml">
	Order Deny,Allow
	Deny from all
	Satisfy all
</Files>
EOF;

fwrite($ht, $patch);
fclose($ht);

echo "Security patch applied.\r\n";

unlink("secpatch.php");

exit(0);
?>
