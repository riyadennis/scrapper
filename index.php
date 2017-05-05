<?php
/**
 * index file that can be called from command line
 */
require_once 'include/class.scraper.php';
$url = "http://www.sainsburys.co.uk/";
$scrapper = new Scrapper($url);
$json_array = $scrapper->createJsonArray();
echo '<pre>';
echo json_encode($json_array, JSON_PRETTY_PRINT);
echo "</pre>";
