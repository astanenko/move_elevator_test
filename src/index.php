<?php
/**
 * Initializes execution of twitter loader.
 * 
 * @package twitter_loader
 * 
 * @author Oleksandr Zhdanenko
 * @email ozhdanenko@gmail.com
 */
require_once "phar://twitter.phar/Application.php";

$configFile = "phar://twitter.phar/config.ini";

try {
    
    if (!file_exists($configFile) || !is_readable($configFile)) {
        throw new Exception("Config file hasn't been found");
    }
    
    $config = parse_ini_file($configFile);
    
    if ($config === false) {
        throw new Exception("Read of config file has been failed");
    }
    
   $app = new Application($config);
   if (php_sapi_name() === 'cli') {
        print $app->getTweetsFromCLI($argv);
   } else {
        $tweets = $app->getTweetsFromRequest($_GET);
   }
   
} catch (Exception $ex) {
    if (php_sapi_name() === 'cli') {
        print "\n Error: " . $ex->getMessage() . "\n"; 
    } else {
        throw $ex;
    }
}