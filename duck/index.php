<?php
/*
 *      ~uck - A minimal nopaste. (View page)
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

require_once '../core.php';
include 'geshi/geshi.php';

$db = simplexml_load_file("../uck.xml");
$method = $_SERVER['REQUEST_METHOD'];
$langs = explode(',', $db->config->langs);
$iu = explode(',', $db->sources->in_use);

if ($method != 'GET'){
	header('Content-type: ' . $db->config->content_type);
	die($db->config->bad_request);
}

if (!isset($_GET['id'])){
	header('Content-type: ' . $db->config->content_type);
	die($db->config->nocode);
} else if ((isset($_GET['id'])) && (!in_array($_GET['id'], $iu))){
	header('Content-type: ' . $db->config->content_type);
	die($db->config->notfound);
}

if (!isset($_GET['lang'])){
	header('Content-type: ' . $db->config->content_type);
	pduck($_GET['id']);
} else if ((isset($_GET['lang'])) && (in_array($_GET['lang'], $langs))){
	header('Content-type: ' . $db->config->content_type_html);
	hduck($_GET['id'], $_GET['lang']);
} else if ((isset($_GET['lang'])) && (!in_array($_GET['lang'], $langs))){
	header('Content-type: ' . $db->config->content_type);
	pduck($_GET['id']);
}

?>
