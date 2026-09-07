<?php
/**
 * @var $test
 * @var array $contacts
 * @var contacts\app\db\QueryProvider $provider
 */
use contacts\app\web\Html;
use contacts\db\models\Contact;
use contacts\app\web\Url;
use contacts\app\db\QueryProvider;
use PHP_CodeSniffer\Generators\HTML as GeneratorsHTML;

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
    <div class="w-full p-8">
        <div>
             <table class="border-collapse border border-gray-400 w-full">
                <thead>
                    <tr>
                    <th class="border border-gray-300">nombre de page</th>
                    <th class="border border-gray-300">numéro de page</th>
                    <th class="border border-gray-300">nombre de lignes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <?php
                        echo Html::tag('td', $provider->getPageNumber(), ['class' => 'border border-gray-300']);
                        echo Html::tag('td', $provider->getPage(), ['class' => 'border border-gray-300']);
                        echo Html::tag('td', $provider->getTotalCount(), ['class' => 'border border-gray-300']);
                    ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="flex flex-row justify-center gap-2">
        <?php
                $currentPage = $provider->getPage();
                $disablePrev = ($currentPage - 1  < 1 );
                $disableNext = ($currentPage + 1  > $provider->getPageNumber());
                for($i=1; $i<$provider->getPageNumber()+1; $i ++){
                        echo Html::beginTag('div', ['class' => 'flex']);
                        $isCurrentPage = ($currentPage === $i);
                        $class = [];
                        $class[] = 'btn btn-md rounded-md';
                        if ($isCurrentPage) {
                            $class[] = 'btn-default';
                        } else {
                            $class[] = 'btn-primary';
                        }
                        $classes = implode(' ', $class);
                        if ($isCurrentPage === true) {
                            echo Html::tag('span', $i, ['class' => $classes]);
                        } else {
                            echo Html::tag('a', $i,
                            [
                                'href' => Url::to(['default', 'page' => $i]),
                                'class' => $classes,
                                'disabled' => ($currentPage === $i)
                            ]);
                        }
                      
                        echo Html::endTag('div');

                }
        ?>
    </div>
    <div class="w-full p-8">
          <table class="border-collapse border border-gray-400 w-full">
                <thead>
                    <tr>
                    <th class="border border-gray-300">Identifiant</th>
                    <th class="border border-gray-300">Nom</th>
                    <th class="border border-gray-300">Email</th>
                    <th class="border border-gray-300">Téléphone</th>
                    <th class="border border-gray-300">Action</th>
                    </tr>
                </thead>
                <tbody>
        <?php
    
        /** @var Contact $contact */
        foreach ($provider->getModels() as $contact):
        ?>
                    <tr>
                    <td class="border border-gray-300 justify-self-center"><?php echo '#'.$contact->id; ?></td>
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
