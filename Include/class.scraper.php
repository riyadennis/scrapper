<?php
/**
 * Class that does the scrapping
 */
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
        if($url != "" && $this->checkUrlExists($url)){
            $this->url = $url;
        }else {
            throw new InvalidArgumentException("Invalid URL");
        }
        
        $this->crawler = $client->request('GET', $this->url);
    }
    /**
     * function that actually creates the array thats json encoded
     * 
     * @return array
     */
    public function createJsonArray()
    {
        $json_array = array();
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
    /**
     * Function that can be used to validate a url 
     * 
     * @param string $url
     * @return boolean
     */
    public function checkUrlExists($url)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_HEADER, 1); //get the header
        curl_setopt($c, CURLOPT_NOBODY, 1); //and *only* get the header
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1); //get the response as a string from curl_exec(), rather than echoing it
        curl_setopt($c, CURLOPT_FRESH_CONNECT, 1); //don't use a cached version of the url
        if (!curl_exec($c)) {
            //echo $url.' inexists';
            return false;
        } else {
            return true;
        }
    }

}
