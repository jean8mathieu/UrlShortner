<?php
/**
 * Created by PhpStorm.
 * User: Jean-Mathieu
 * Date: 3/1/2016
 * Time: 8:45 PM
 */


header('Content-Type: application/json');

include("API.php");

$url = @$_GET['url'];
$method = @$_GET['method'];

$limit = @$_GET['limit'];

$api = new API();
switch ($method) {
    case "insert":
        echo $api->insertURL($url);
        break;
    case "read":
        echo $api->getURLLimit($limit);
        break;
    default:
        echo json_encode(array('error' => 'true', 'result' => 'Error 101'));
}
