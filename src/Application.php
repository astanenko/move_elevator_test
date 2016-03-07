<?php
/**
 * Holds Application class
 * 
 * @package twitter_loader
 * 
 * @author Oleksandr Zhdanenko
 * @email ozhdanenko@gmail.com
 */
require "phar://twitter.phar/vendor/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Application class
 * 
 * Application class loads tweets through twitter API
 * uses Abraham TwitterOAuth
 *
 * @author Oleksandr Zhdanenko
 * @email ozhdanenko@gmail.com
 */
class Application {
    
    /**
     * @const int Defines default number of tweets to load
     */
    const DEFAULT_TWEETS_COUNT = 10;
    
    /**
     * @var string consumer key for twitter application. Part of login credentials
     */
    private $consumerKey = null;
    
    /**
     * @var string consumer secret key for twitter application. Part of login credentials
     */
    private $consumerSecret = null;
    
    /**
     * @var TwitterOAuth OAuth connection
     */
    private $connection = null;
    
    /**
     * Constructor. Sets login credentials
     * 
     * @param array $config configuration array
     * @throws Exception
     * 
     * @return void
     */
    public function __construct($config) {
        if (empty($config['consumerKey'])) {
            throw new Exception("Consumer key is not set in config file.");
        }
        if (empty($config['consumerSecret'])) {
            throw new Exception("Consumer sercret key is not set in config file.");
        }
        $this->consumerKey = $config['consumerKey'];
        $this->consumerSecret = $config['consumerSecret'];
    }
    
    /**
     * Get tweets by passed in parameters
     * 
     * @param string $hashtag hashtag to search
     * @param int $numberOfTweets number of tweets to return
     * @param string $language Language code to search
     * @throws Exception
     * 
     * @return stdClass response object
     */
    public function getTweets($hashtag,$numberOfTweets = self::DEFAULT_TWEETS_COUNT,$language = null) {
        $search = trim(strip_tags($hashtag));
        if (empty($search)) {
            throw new Exception("Hashtag cann't be empty");
        }
        if (!is_int($numberOfTweets) && !ctype_digit($numberOfTweets)) {
            throw new Exception("Number of tweets to display should be an integer");
        }
        
        $params = ["q" => $search,"count" => $numberOfTweets];
        if (!empty($language)) {
            $params['lang'] = $language;
        }
        $path = "search/tweets";
        
        return $this->sendGetRequest($path,$params);
    }
    
    /**
     * Gets tweets, when called from CLI
     * @param array $arguments CLI arguments
     * @throws Exception
     * 
     * @return string Formatted output
     */
    public function getTweetsFromCLI($arguments) {        
        if (count($arguments) == 1 || (count($arguments) == 2 && $arguments[1] == '-help')) {
            $outputArray = [
                "",
                "Twitter loader. Version 1.0",
                "",
                "Usage:",
                "twitter.phar [hashtag] [number of tweets] [language]",
                "",
                " -hashtag                  Hashtag to search. String. *Mandatory",
                " -number of tweets         Number of search results to show. Integer. *Mandatory",
                " -language                 Restricts tweets to the given language, given by an ISO 639-1 code. String",
                "",
            ];
            
            return implode("\n",$outputArray);
        } else if (count($arguments) < 3) {
            throw new Exception("You have to specify at least hashtag and number of tweets.");
        }
        
        $response = $this->getTweets($arguments[1], $arguments[2], !empty($arguments[3]) ? $arguments[3] : null);
        
        return $this->formatOutputForCLI($response);
    }
    
    /**
     * Get tweets, when called through http request
     * @param array $get array of GET parameters
     * @throws Exception
     * 
     * @return array Formatted array of loaded tweets
     */
    public function getTweetsFromRequest($get) {
        if (isset($get['submit'])) {
            if (empty($get['hashtag']) || empty($get['count'])) {
                throw new Exception("You have to specify at least hashtag and number of tweets.");
            }
            $response = $this->getTweets($get['hashtag'], $get['count'], !empty($get['lang']) ? $get['lang'] : null);
            $tweets = $this->formatOutputForUI($response);
        } else {
            $tweets = array();
        }
         
        return $tweets;
    }
    
    /**
     * Formats loaded tweets for display in UI
     * @param stdCLass $response response object
     * 
     * @return array formatted array of loaded tweets
     */
    private function formatOutputForUI($response) {
        $result = array();
        $this->checkResponseForValidity($response);
        if (!empty($response->statuses)) {
            foreach ($response->statuses as $tweet) {
                $result[]= array(
                    'created' => $tweet->created_at,
                    'author' => $tweet->user->screen_name,
                    'text' => $tweet->text
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Checks if response is valid
     * @param stdClass $response response object
     * @throws Exception
     * 
     * @return void
     */
    private function checkResponseForValidity($response) {
        if (!is_object($response) || !isset($response->statuses) || !is_array($response->statuses)) {
            if (!empty($response->errors)) {
                $errors = array();
                foreach ($response->errors as $error) {
                    $errors[] = $error->message;
                }
                $errorsText = implode(", ", $errors);
            } else {
                $errorsText = "Recieved response from Twitter is not valid";
            }
            throw new Exception($errorsText);
        }
    }
    
    /**
     * Formats output for display in CLI
     * @param stdCLass $response response object
     * 
     * @return string formatted CLI output cstring
     */
    private function formatOutputForCLI($response) {
        $this->checkResponseForValidity($response);
        
        $result = "";
        
        if (empty($response->statuses)) {
            $result .= "\n" . "No tweets have been found for specified criteria." . "\n";
        } else {
            foreach ($response->statuses as $tweet) {
                $result .= "\n-- " . $tweet->created_at . "\n" . "-- From " . $tweet->user->screen_name ."\n\n" . $tweet->text . "\n\n" . "####################" . "\n";
            }
        }
        
        return $result;
    }
    
    /**
     * Estabilishes OAuth connection to twitter
     * @throws Exception
     * 
     * @return void 
     */
    private function connectToTwitter() {
        // Certificate file should be copied to working directory, because CURL can't get access to contents of phar archive
        if (!file_exists('cacert.pem')) {
            if (!is_writable('./')) {
                throw new Exception("You should have write permissions for working directory to execute this app");
            }
            copy('phar://twitter.phar/vendor/abraham/twitteroauth/src/cacert.pem','cacert.pem');
        }
        $this->connection = new TwitterOAuth($this->consumerKey, $this->consumerSecret);
        $success = $this->connection->get("account/verify_credentials");
        
        if (!$success) {
            throw new Exception("Couldn't connect to Twiiter. Please check your internet connection, or login credentials");
        }
        
    }
    
    /**
     * Constructor. Sets login credentials
     * @param string $path twitter interal path to load
     * @param array $params array of params to send to twitter
     * 
     * @return stdClass response object
     */
    private function sendGetRequest($path,Array $params = []) {
        !is_null($this->connection) || $this->connectToTwitter();
        
        return $this->connection->get($path,$params);
    }
}