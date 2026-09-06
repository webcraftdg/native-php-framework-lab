<?php

namespace contacts\webapp\controllers;

use contacts\App;
use contacts\app\db\QueryProvider;
use contacts\app\web\Controller;
use contacts\db\models\Contact;
use Exception;

class DefaultController extends Controller
{


    public function defaultAction() : mixed
    {
        try {
            $contacts = Contact::findAll();
            $query = Contact::find()->orderBy(['lastname' => 'ASC', 'firstname' => 'DESC']);
            $provider = new QueryProvider(
                query:$query
            );

            return $this->render(
                'default',
                [
                    'provider' => $provider
            ]);
        } catch(Exception $e) {
            throw $e;
        }
    }

    public function createAction() : mixed
    {
        try {
            $contact = new Contact();
            $request = App::$app->getRequest();
            if ($request->isPost() === true) {
                $body = $request->getBodyParams();
                $contact->load($body);
                if($contact->validate() === true) {
                    $contact->dateCreate = date('Y-m-d H:s:i');
                    $contact->dateUpdate = date('Y-m-d H:s:i');
                    $success = $contact->save();
                }
            }
            return $this->render(
                'create',
                [
                    'contact' => $contact
                ]
            );
        } catch(Exception $e) {
            throw $e;
        }
    }

    public function updateAction(int $id) : mixed
    {
        try {
            $request = App::$app->getRequest();
            /** @var Contact $contact */
            $contact = Contact::findOne($id);
            if ($request->isPost() === true) {
                $body = $request->getBodyParams();
                $contact->load($body);
                if($contact->validate() === true) {
                    $contact->dateUpdate = date('Y-m-d H:s:i');
                    $success = $contact->save();
                }
            }
            return $this->render(
                'edit',
                [
                    'contact' => $contact
                ]
            );
        } catch(Exception $e) {
            throw $e;
        }
    }
}
