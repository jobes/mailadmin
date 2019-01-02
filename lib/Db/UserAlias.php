<?php
namespace OCA\MailAdmin\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class UserAlias extends Entity implements JsonSerializable {

    protected $alias;
    protected $email;
    protected $validto;

    public function jsonSerialize() {
        return [
            'alias' => $this->alias,
            'email' => $this->email,
            'validto' => $this->validto
        ];
    }
}