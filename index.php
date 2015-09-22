<?php
/**
 * index file that can be called from command line
 */
require_once 'include/class.scraper.php';
$url = "http://www.sainsburys.co.uk/webapp/wcs/stores/servlet/CategoryDisplay?msg=&langId=44&categoryId=185749&storeId=10151&krypto=XkNJuRN9KLq6n6uH5Mz8vPJfKTPApwG8ucakhoeAQfAFnb5qGoUjeNIcE37XJqsLFKgqJpn0KSE2%0A4jWWsyGCqL9MrFzaSCurcTmEROPW2THpmiThWdfCBMkVMSAzC8evzTjPcnXztC7gZGu6swc%2BYE%2Bg%0Ar8dazzckCO9eiVbSKD7I%2BqoK45FoyfB5vK58kXkI%2FokZVqWZgIcEc7yXITiZeunS4A409YSjTfwF%0A9Y8J5255LV9jsLZBkMAnB%2Fl6zy53JRXhDEg%2BB7w7Lls7d16DjbsU0i4zzK4W15E%2BSTHddb4%3D#langId=44&storeId=10151&catalogId=10137&categoryId=185749&parent_category_rn=12518&top_category=12518&pageSize=20&orderBy=FAVOURITES_FIRST&searchTerm=&beginIndex=0&hideFilters=true";
$scrapper = new Scrapper($url);
$json_array = $scrapper->createJsonArray();
echo '<pre>';
echo json_encode($json_array, JSON_PRETTY_PRINT);
echo "</pre>";
