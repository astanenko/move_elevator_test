# move_elevator_test

This is Tweets loader version 1.0

h2. Usage

* To call it from command line, use php twitter.phar hashtag_to_search number_of_tweets language
* Hashtag and number of tweets are mandatory, language parameter can be skipped
* To call help menu just ignore all parameters or type php twitter.phar -help
* To run an application, you should have write permissions to current directory. It's argued by impossibility for CURL to get an access to files inside of phar archive, such as Certificate authority. Therefore it should be extracted by php script before sending request to twitter.

* To call it using webserver, place twitter.phar to the ui directory
* All the contents of ui directory should be copied to the webroot, or any nested directory

* To run tests, place twitter.phar to the same directory as ApplicationTest.php
