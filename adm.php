<?php
/*
 *      ~uck - A minimal nopaste. (Administration page)
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

require_once 'core.php';

$db = simplexml_load_file("uck.xml");
$method = $_SERVER['REQUEST_METHOD'];
$langs = explode(',', $db->config->langs);
$iu = explode(',', $db->sources->in_use);

header('Content-type: ' . $db->config->content_type);

if ($method != 'POST'){
	die($db->config->adm_get."\r\n");
}

isset($_POST['pass']) ? vpass($_POST['pass']) : die($db->config->adm_get."\r\n");
isset($_POST['act']) ? act($_POST['act']) : die("No action specified.\r\n");

?>
