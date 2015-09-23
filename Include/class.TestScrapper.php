<?php

namespace TestNamespace;

/*
 * Class to test Scrapper class
 */
require 'class.scraper.php';
require 'vendor/autoload.php';

Class TestScrapper extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the main function that actually do the scrapping and creates an array
     * 
     */
    public function testCreateJsonArray()
    {
//       $url = "http://www.sainsburys.co.uk/";
        $url = "http://www.sainsburys.co.uk/webapp/wcs/stores/servlet/CategoryDisplay?msg=&langId=44&categoryId=185749&storeId=10151&krypto=XkNJuRN9KLq6n6uH5Mz8vPJfKTPApwG8ucakhoeAQfAFnb5qGoUjeNIcE37XJqsLFKgqJpn0KSE2%0A4jWWsyGCqL9MrFzaSCurcTmEROPW2THpmiThWdfCBMkVMSAzC8evzTjPcnXztC7gZGu6swc%2BYE%2Bg%0Ar8dazzckCO9eiVbSKD7I%2BqoK45FoyfB5vK58kXkI%2FokZVqWZgIcEc7yXITiZeunS4A409YSjTfwF%0A9Y8J5255LV9jsLZBkMAnB%2Fl6zy53JRXhDEg%2BB7w7Lls7d16DjbsU0i4zzK4W15E%2BSTHddb4%3D#langId=44&storeId=10151&catalogId=10137&categoryId=185749&parent_category_rn=12518&top_category=12518&pageSize=20&orderBy=FAVOURITES_FIRST&searchTerm=&beginIndex=0&hideFilters=true";
        $scrapper = new \Scrapper($url);
        $json_array = $scrapper->createJsonArray();
        $this->assertNotEmpty($json_array);
    }
    /*
     * Function to test cleanData which removes unwanted chars from title and price
     * 
     */
    public function testCleanData()
    {
        $scrapper = new \Scrapper("http://www.sainsburys.co.uk/");
        $cleaned = $scrapper->cleanData("%%clean%%");
        $cleaned_price = $scrapper->cleanData("3.4/t/t");
        $this->assertNotContains('\t', $cleaned_price);
        $this->assertNotContains('$', $cleaned);
    }
    /**
     * tests getSize function
     * ANy url given it should give a value
     */
    public function testgetSize()
    {
        $scrapper = new \Scrapper("http://www.sainsburys.co.uk/");
        $size = $scrapper->getSize("http://www.sainsburys.co.uk/");
        $this->assertNotNull($size);
    }
    /*
     * Test checkUrlExists function in the scrapper class
     * if bad url returns false hence have put assertfalse
     * if good url return tru hance asserttrue
     * 
     */
    public function testCheckurlexists()
    {
        $scrapper = new \Scrapper("http://www.sainsburys.co.uk/");
        $exists = $scrapper->checkUrlExists("http://www.sainsburys.co.uk/");
        $this->assertTrue($exists);
        $not_exists = $scrapper->checkUrlExists("http://www.testme");
        $this->assertFalse($not_exists);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testException()
    {
        $scrapper = new \Scrapper("dfdgfdf");
        $this->setExpectedException('InvalidArgumentException');
    }

}
