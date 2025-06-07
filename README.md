# Twitter loader version 1.0

Is a test task for one of the interviews

## Usage

* To call it from command line, use php twitter.phar hashtag_to_search number_of_tweets language
* Hashtag and number of tweets are mandatory, language parameter can be skipped
* To call help menu just ignore all parameters or type php twitter.phar -help
* To run an application, you should have write permissions for current directory. It's argued by impossibility of CURL getting an access to the files inside of phar archive, such as certificate authority. Therefore it should be extracted by php script before sending request to Twitter.
* To call it using webserver, place twitter.phar to the ui directory
* All the contents of ui directory should be copied to the webroot, or any nested directory, and afterwards called from browser
* To run tests, place twitter.phar in the same directory as ApplicationTest.php, and run phpunit ApplicationTest.php.
