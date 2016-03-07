<?php
/**
 * Holds ApplicationTest class
 * 
 * @package twitter_loader
 * 
 * @author Oleksandr Zhdanenko
 * @email ozhdanenko@gmail.com
 */
require_once "phar://twitter.phar/Application.php";


/**
 * Test case for Application class
 * 
 *
 * @author Oleksandr Zhdanenko
 * @email ozhdanenko@gmail.com
 */
class ApplicationTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @var Application holds object of tested class
     */
    var $application = null;
    
    /**
     * Creates an object of Application class
     */
    function setUp() {
        $this->application = new Application(["consumerKey" => "XWLSqw2K8rP0a8hkuMxcv4iDp","consumerSecret" => "oqIBwbrTPS6fN4Ufg0WSXJGT40aLVs4YxSVlo6l1GxnEQUQ9cp"]);
    }
    
    /**
     * Checks if getTweets function returns valid response object
     */
    function testGetTweets() {
        $result = $this->application->getTweets("testing",1,"en");
        $this->assertTrue(is_object($result));
    }
    
    /**
     * Checks if getTweetsFromCLI returns valid formated string
     */
    function testGetTweetsFromCLI() {
        $params = ["index.php","testing","1","en"];
        $result = $this->application->getTweetsFromCLI($params);
        $this->assertTrue(is_string($result));
    }
    
    /**
     * Checks if getTweetsFromRequesst returns valid formatted array
     */
    function testGetTweetsFromRequest() {
        $get = ["hashtag" => "testing", "count" => "1", "lang" => "en"];
        $result = $this->application->getTweetsFromRequest($get);
        $this->assertTrue(is_array($result));
    }
    
}