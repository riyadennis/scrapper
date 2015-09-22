<?php

require 'vendor/autoload.php';

use Goutte\Client;

class Scrapper
{

    /**
     *URL to be scrapped
     * 
     * @var string 
     */
    public $url;
    
    /**
     *Crawler object
     * @var string 
     */
    public $crawler;

    /**
     * Set the url and crawler.
     * 
     * @param string $url
     */
    public function __construct($url)
    {
        $client = new Client();
        $this->url = $url;
        $this->crawler = $client->request('GET', $this->url);
    }
    /**
     * function that actually creates the array thats json encoded
     * 
     * @return array
     */
    public function createJsonArray()
    {
        $title = $this->crawler->filterXPath('//div[@class="productInfoWrapper"]')->each(function($node) {
            $trimmed = trim($node->text());
            return str_replace(array('\r', '\n', '\t'), "", $trimmed);
        });
        $price = $this->crawler->filterXPath('//p[@class="pricePerUnit"]')->each(function($node, $crawler) {
            $trimmed = trim($node->text());
            return str_replace(array('\r', '\n', '\t', '\u'), "", $trimmed);
        });
        for ($i = 0; $i < count($title); $i++) {
            $link = $this->crawler->selectLink($title[$i]);
            if (!empty($link->links())) { //if there is valid link 
                $uri = $link->link()->getUri();
                $size = $this->getSize($uri);
            }
            $title_cleaned = $this->cleanData($title[$i]);
            $price_cleaned = $this->cleanData($price[$i],true);
            $json_array[] = array('title' => $title_cleaned,
                'unit_price' => $price_cleaned,
                'size' => $size,
                'description' => $title_cleaned);
        }
        return $json_array;
    }
    /**
     * Function that cleans invalid characters from a string
     * 
     * @param type $str
     * @param type $numbers
     */
    public function cleanData($str, $numbers = false)
    {
        $invalid_characters = array("$", "%", "#", "<", ">", "|", '\r');

        if ($numbers) {
            $str_cleaned = preg_replace('/[^0-9\.-]/', '', $str);
        } else {
            $str_cleaned = str_replace($invalid_characters, "", $str);
            $str_cleaned = preg_replace('/(\v|\s)+/', ' ', $str_cleaned);
        }
        return $str_cleaned;
    }
    /**
     * Function that calculated the size  from the url.
     * 
     * @param string $uri
     * @return string
     */
    public function getSize($uri)
    {
        $useragent = 'Firefox (WindowsXP) - Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $html = curl_exec($ch);
        $kilobites = round(mb_strlen($html) / 1024, 3);
        return $kilobites . "kb";
    }

}

$url = "http://www.sainsburys.co.uk/webapp/wcs/stores/servlet/CategoryDisplay?msg=&langId=44&categoryId=185749&storeId=10151&krypto=XkNJuRN9KLq6n6uH5Mz8vPJfKTPApwG8ucakhoeAQfAFnb5qGoUjeNIcE37XJqsLFKgqJpn0KSE2%0A4jWWsyGCqL9MrFzaSCurcTmEROPW2THpmiThWdfCBMkVMSAzC8evzTjPcnXztC7gZGu6swc%2BYE%2Bg%0Ar8dazzckCO9eiVbSKD7I%2BqoK45FoyfB5vK58kXkI%2FokZVqWZgIcEc7yXITiZeunS4A409YSjTfwF%0A9Y8J5255LV9jsLZBkMAnB%2Fl6zy53JRXhDEg%2BB7w7Lls7d16DjbsU0i4zzK4W15E%2BSTHddb4%3D#langId=44&storeId=10151&catalogId=10137&categoryId=185749&parent_category_rn=12518&top_category=12518&pageSize=20&orderBy=FAVOURITES_FIRST&searchTerm=&beginIndex=0&hideFilters=true";
$scrapper = new Scrapper($url);
$json_array = $scrapper->createJsonArray();
echo '<pre>';
echo json_encode($json_array, JSON_PRETTY_PRINT);
echo "</pre>";
