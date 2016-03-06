<?php
/**
 * GUI for twitter loader
 * 
 * @package tweeter_loader
 * 
 * @author Oleksandr Zhdanenko
 * @email ozhdanenko@gmail.com
 */
$availableLanguages = array(
    ''   => 'All languages',
    'en' => 'English',
    'de' => 'German',
    'ru' => 'Russian',
    'it' => 'Italian'
 );
    
try {
    require_once "phar://tweeter.phar/index.php";
    /* @var $tweets array */
} catch (Exception $ex) {
    $error = $ex->getMessage();
    $tweets = array();
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
    <head>
        <link href="bootstrap/css/bootstrap.css" rel="stylesheet" />
        <style>
            .error {
                color:red;
            }
            .tweets_container .row-fluid {
                margin-bottom:20px;
            }
            .tweets_container .row-fluid:last-child {
                margin-bottom: 0;
            }
            .tweets_container .row-fluid > div {
                background: #f9f9f9;
                border-radius: 8px;
                padding:15px;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span12">
                    <div class="hero-unit">
                        <h1>Tweets loader v 1.0</h1>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <form acion="" method="get" class="form-inline">
                            <input type="text" name="hashtag" value="<?=!empty($_GET['hashtag']) ? $_GET['hashtag'] : ''?>" placeholder="Hashtag">
                            <input type="text" name="count" value="<?=!empty($_GET['count']) ? $_GET['count'] : ''?>" placeholder="Number of tweets to display">
                            <select name="lang">
                                <!-- <option value="" disabled selected>Language</option> -->
                                <?php foreach ($availableLanguages as $langKey => $language): ?>
                                <option value="<?=$langKey?>"<?=(!empty($_GET['lang']) && $_GET['lang'] == $langKey ? ' selected="selected"' : '')?>><?=$language?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="submit" name="submit" class="btn btn-primary" value="Find" />
                    </form>
                </div>
            </div>
            <?php if (!empty($error)): ?>
            <div class="row-fluid">
                <div class="span12">
                    <h4 class="error"><?=$error;?></h4>
                </div>
            </div>
            <?php endif; ?>
            <div class="tweets_container">
                <div class="row-fluid">
                    <?php foreach ($tweets as $counter => $tweet): ?>

                    <?php if ($counter > 0 && $counter % 4 == 0): ?>
                    </div>
                    <div class="row-fluid">
                    <?php endif; ?>

                    <div class="span3">
                        <p><i class="icon-user"></i> <?=$tweet['author']?></p>
                        <p><i class="icon-calendar"></i> <?=$tweet['created']?></p>
                        <p><?=$tweet['text']?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </body>
</html>