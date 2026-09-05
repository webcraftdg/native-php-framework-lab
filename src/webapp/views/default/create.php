<?php
/**
 * @var $test
 * @var $contact
 */
use contacts\app\web\Html;
use contacts\app\web\Url;

?>
<h1>Contact</h1>
<?php
    echo Html::tag('a', 'Retour', ['class' => 'btn btn-primary', 'href' => Url::to(['/default'])]);
    echo Html::beginTag('form', ['method' => 'post', 'action' => '']) ;
    echo Html::inputModelPdo($contact, 'input', 'lastname', ['placeholder' => 'Votre nom', 'class' => 'form-input', 'type' => 'text']);
    echo Html::inputModelPdo($contact, 'input', 'firstname', ['placeholder' => 'Votre prénom', 'class' => 'form-input', 'type' => 'text']);
    echo Html::inputModelPdo($contact, 'input', 'email', ['placeholder' => 'Votre email', 'class' => 'form-input', 'type' => 'text']);
    echo Html::inputModelPdo($contact, 'input', 'phone', ['placeholder' => 'Votre téléphone', 'class' => 'form-input', 'type' => 'text']);
    echo Html::tag('button', 'Envoyer', ['type' => 'submit']);
    echo Html::endTag('form');
?>
<div class="error-container">
    <?php foreach ($contact->errors as $attribute => $error): ?>
        <div class="error">
            <strong><?php echo $attribute ?>:</strong>
            <ul>
                    <li><?php echo $error ?></li>
            </ul>
        </div>
    <?php endforeach; ?>
</div>
