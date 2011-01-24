<?php
/*
 *      ~uck - A minimal nopaste. (Core functions.)
 *      
 *      Copyright 2010 d0mhc <phbn@live.jp>
 *      
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *      
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *      
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */

error_reporting(E_ALL);

/**
 *************************** Index page functions *******************************
*/

function init(){
	$db = simplexml_load_file("uck.xml");
	$method = $_SERVER['REQUEST_METHOD'];
	switch ($method){
		case 'GET':
			uck();
			break;
		case 'POST':
			fuck();
			break;
		default:
			uck();
			break;
	}
}

function codegen(){
	$db = simplexml_load_file("uck.xml");
	$cvar = explode(',', $db->config->codevar);
	$vlen = sizeof($cvar)-1;
	$clen = intval($db->config->codelen);
	$iu = explode(',', $db->sources->in_use);
	$i = 0;
	
	while (1){
		$tmp = '';
		shuffle($cvar);
		$atmp = array_rand($cvar, $clen);
		foreach ($atmp as $choice){
			$tmp .= strval($cvar[$choice]);
		}
		if (!in_array($tmp, $iu)){
			break;
		}
	}
	return $tmp;
}

function duckpath(){
	$pre = 'http://' . $_SERVER['HTTP_HOST'];
	$uri = $_SERVER['REQUEST_URI'];
	$in = ereg($_SERVER['SCRIPT_NAME'], $uri) ? true : false;
	if ($in){
		$dir = explode('/', $uri);
		array_pop($dir);
		$dir = implode('/', $dir);
		return $pre . "$dir/duck";
	} else {
		return $pre . $uri."duck";
	}
}

function uck(){
	$db = simplexml_load_file("uck.xml");
	$isize = filesize($db->config->ifile);
	$index = fopen($db->config->ifile, "r");
	$itext = fread($index, $isize);
	fclose($index);
	$langs = explode(',', $db->config->langs);
	$llangs = "";
	$lpr = $db->config->langs_per_row;
	$c = 0;
	foreach ($langs as $lang){
		if ($c == $lpr){
			$llangs .= "\r\n";
			$c = 0;
		}
		$llangs .= " $lang";
		$c++;
	}
	$loc = duckpath();
	$itext = str_replace('{langs}', $llangs, $itext);
	$itext = str_replace('{local}', str_replace('duck', '', $loc), $itext);
	header('Content-type: ' . $db->config->content_type);
	echo $itext;
	return 0;
}

function fuck(){
	$db = simplexml_load_file("uck.xml");
	$fucked = isset($_POST['fuck']);
	$iu = explode(',', $db->sources->in_use);
	if (!$fucked){
		uck();
	} else {
		$fuck = htmlspecialchars(base64_encode($_POST['fuck']));
	}
	if (strlen($fuck) < 1){
		uck();
	} else {
		header('Content-type: ' . $db->config->content_type);
		$code = codegen();
		$cod = $db->sources->addChild("src_$code", $fuck);
		array_push($iu, $code);
		$iu = implode(',', $iu);
		$db->sources->in_use = $iu;
		$db->asXML("uck.xml");
		echo duckpath() . '/?id=' . $code . "\n";
	}
	return 0;
}


/**
 *************************** View page functions *******************************
*/

function pduck($id){
	$id = "src_$id";
	$db = simplexml_load_file("../uck.xml");
	$source = htmlspecialchars_decode(base64_decode($db->sources->$id));
	echo $source;
	return 0;
}

function hduck($id, $lang){
	$id = "src_$id";
	$db = simplexml_load_file("../uck.xml");
	$source = base64_decode($db->sources->$id);
	$geshi = new GeSHi($source, $lang);
	$geshi->enable_keyword_links(false);
	$geshi->set_tab_width(intval($db->config->tab_length));
	$geshi->set_keyword_group_style(1, 'font-weight: bold;', true);
	$geshi->set_keyword_group_style(2, 'font-weight: bold;', true);
	$geshi->set_keyword_group_style(3, 'font-weight: bold;', true);
	$geshi->set_keyword_group_style(4, 'font-weight: bold;', true);
	$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
	echo <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>$id</title>
</head>
<body>
EOF;
	echo $geshi->parse_code();
	echo <<<EOF
</body>
</html>
EOF;
	return 0;	
}



/**
 *************************** Admin page functions *******************************
*/

function act($act){
	if ($act == 'list'){
		isset($_POST['ln']) ? listposts(true) : listposts(false);
	} else if ($act == 'del'){
		isset($_POST['id']) ? delpost($_POST['id']) : die("No post ID to delete specified.\r\n");
	} else if ($act == 'chpass'){
		isset($_POST['new']) ? chpass($_POST['new']) : die("No new password specified.\r\n");
	} else {
		die("Invalid parameters.\r\n");
	}
}

function listposts($ln){
	$db = simplexml_load_file("uck.xml");
	$iu = explode(',', $db->sources->in_use);
	$c = 0;
	foreach ($iu as $id){
		if ($c == 7){
			$c = 0;
			$lnd = duckpath() . "/?id=$id\r\n";
			if ($ln == true) { echo $lnd; } else { echo "$id\r\n"; }
		} else {
			$lnd = duckpath() . "/?id=$id, ";
			if ($ln == true) { echo $lnd; } else { echo "$id , "; }
			$c++;
		}
	}
	echo "\r\n";
	return 0;
}

function delpost($id){
	$db = simplexml_load_file("uck.xml");
	$cid = "src_$id";
	$iu = explode(',', $db->sources->in_use);
	if (!in_array($id, $iu)){
		die($db->config->notfound);
	} else {
		$s = array_search($id, $iu);
		unset($s);
		unset($db->sources->$cid);
		$db->sources->in_use = implode(',', $iu);
		echo "Source with ID $id deleted.\r\n";
	}
	return 0;
}

function vpass($pass){
	$db = simplexml_load_file("uck.xml");
	if ($db->config->passwd != md5(md5(md5(md5(md5(md5(md5("the_admin_password_is $pass and_now_try_to_crack_it_poor_n00b!")))))))){
		die($db->config->wrong_pass."\r\n");
	}
	return 0;
}
		
function chpass($new){
	$db = simplexml_load_file("uck.xml");
	$db->config->passwd = md5(md5(md5(md5(md5(md5(md5("the_admin_password_is $new and_now_try_to_crack_it_poor_n00b!")))))));
	$db->asXML("uck.xml");
	echo "Password changed.\r\n";
	return 0;
}

?>
