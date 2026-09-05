<?php
/**
 * main.php
 *
 * PHP Version 8.2+
 *
 * @version XXX
 * @package webapp\views\layouts
 *
 * @var $this View
 * @var $content string
 */

use contacts\App;
use contacts\app\web\Html;
use contacts\webapp\assets\AppAsset;
$class = 'wrapper';
$baseUrl = AppAsset::register($this)->basePath;
?>
    <!DOCTYPE html>
    <?php echo Html::beginTag('html', ['lang' => App::$app->language]); ?>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>
            <?php echo App::$app->name; ?>
        </title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?php 
            echo $this->metaTag(['name' => 'X-Version', 'content' => '1.0.0']);
            echo $this->head();        
        ?>
    </head>
<?php echo Html::beginTag('body', ['class' => '']); ?>
<?php
echo $content;
echo $this->endPage();
?>
<?php echo Html::endTag('body'); ?>
<?php echo Html::endTag('html');
?>