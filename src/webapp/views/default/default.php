<?php
/**
 * @var $test
 * @var array $contacts
 */
use contacts\app\web\Html;
use contacts\db\models\Contact;
use contacts\app\web\Url;

?>
<main role="main" id="main">
    <div class="flex header">
        <h1 class="flex-1 title" >Contact List</h1>
        <div class=" flex-2">
            <?php 
                 echo Html::tag(
                    'a',
                    'Créer un contact',
                    [
                        'class' => 'btn btn-default btn-md rounded-md',
                        'href' => Url::to('/default/create'),
                    ]
                );

            ?>
        </div>
    </div>
    <div class="w-full">
          <table class="border-collapse border border-gray-400 w-full">
                <thead>
                    <tr>
                    <th class="border border-gray-300">Nom</th>
                    <th class="border border-gray-300">Email</th>
                    <th class="border border-gray-300">Téléphone</th>
                    <th class="border border-gray-300">Action</th>
                    </tr>
                </thead>
                <tbody>
        <?php
    
        /** @var Contact $contact */
        foreach ($contacts as $contact):
        ?>
                    <tr>
                    <td class="border border-gray-300"><?php echo ucfirst($contact->lastname).' '.ucfirst($contact->firstname); ?></td>
                    <td class="border border-gray-300"><?php echo $contact->email; ?></td>
                    <td class="border border-gray-300"><?php echo $contact->phone; ?></td>
                    <td class="border border-gray-300 justify-center">
                        <?php echo Html::tag(
                            'a',
                            'Editer',
                            [
                                'class' => 'btn btn-sm btn-primary',
                                'href' => Url::to(['/default/update', 'id' => $contact->id])
                            ]); 
                        ?>

                    </td>
                    </tr>
        <?php endforeach; ?>
        </tbody>
        </table>
    </div>
    
</main>
