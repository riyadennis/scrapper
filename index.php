<?php
/**
 * index file that can be called from command line
 */


use Scrapper\Src\ScrapperLib;

require_once 'src/ScrapperLib.php';
$url = "http://www.sainsburys.co.uk/";
$scrapper = new ScrapperLib($url);

$json_array = $scrapper->createJsonArray();
echo '<pre>';
echo json_encode($json_array, JSON_PRETTY_PRINT);
echo "</pre>";
