<?php
/**
 * 
 */
namespace contacts\db\models;

use contacts\app\db\PdoModel;
use Override;

final class Contact extends PdoModel
{
    public $id;
    public $lastname;
    public $firstname;
    public $email;
    public $phone;
    public $dateCreate;
    public $dateUpdate;

    /**
     * get Table name
     *
     * @return string
     */
    public static function getTableName(): string
    {
        return 'contacts';
    }

    public function validationRules(): array
    {
        return [
            [['email', 'lastname', 'firstname', 'phone'], 'required' , 'message' => '{{attribute}} est obligatoire'],
            [['email'], 'email', 'message' => 'email pas bien'],
            [['lastname'], 'string', 'message' => 'lastname pas bien'],
            [['phone'], 'match', 'pattern' => '/^[0-9]{10}$/', 'message' => 'phone pas correct'],
        ];
    }

}