<?php
/**
 * @var $test
 * @var $contact
 */
use contacts\app\web\Html;
use contacts\app\web\Url;

?>
<main role="main" id="main">
    <div class="header flex">
        <di class="flex-1 title"><h1>Contact</h1></di>
        <di class="flex-2"><?php echo Html::tag('a', 'Retour', [
            'class' => 'btn btn-primary btn-md rounded-md',
            'href' => Url::to(['/default'])]); ?></di>
    </div>
    <?php 
        echo $this->render('_form', ['contact' => $contact]);
    ?>
</main>
