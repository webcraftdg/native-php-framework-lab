<?php
/**
 * @var $contact
 */
use contacts\app\web\Html;

?>
  <?php 
        echo Html::beginForm(['method' => 'post', 'class' => 'form']) ;
     ?>
    <div class="grid">
        <div class="form-group">
            <?php
                echo Html::labelModelPdo($contact, 'lastname', ['class' => 'input-label']);
                echo Html::inputModelPdo($contact, 'lastname', ['placeholder' => 'Votre nom', 'class' => 'form-input', 'type' => 'text', 'addErrorTag' => true]);
            ?>
        </div>
          <div class="form-group">
            <?php
                echo Html::labelModelPdo($contact, 'firstname', ['class' => 'input-label']);
                echo Html::inputModelPdo($contact, 'firstname', ['placeholder' => 'Votre prénom', 'class' => 'form-input', 'type' => 'text', 'addErrorTag' => true]);
            ?>
        </div>
          <div class="form-group">
            <?php
                echo Html::labelModelPdo($contact, 'email', ['class' => 'input-label']);
                echo Html::inputModelPdo($contact, 'email', ['placeholder' => 'Votre email', 'class' => 'form-input', 'type' => 'text', 'addErrorTag' => true]);
            ?>
        </div>
         <div class="form-group">
            <?php
                echo Html::labelModelPdo($contact, 'phone');
                echo Html::inputModelPdo($contact, 'phone', [
                    'placeholder' => 'Votre téléphone',
                     'class' => 'form-input',
                      'type' => 'text', 
                      'addErrorTag' => function() use($contact) {
                            return Html::tag('p', $contact->getError('phone'), ['class' => 'error-message']);
                      }]);
            ?>
        </div>
        <div class="form-button">
            <?php
                    echo Html::tag('button', 'Envoyer', ['type' => 'submit', 'class' => 'btn btn-md btn-validate rounded-md']);
                ?>
        </div>
  
    </div>
    <?php 
            echo Html::endform();
    ?>
