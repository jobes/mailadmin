<?php
namespace OCA\MailAdmin\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class User extends Entity implements JsonSerializable {

    protected $uid;
    public function jsonSerialize() {
        return [
            'uid' => $this->uid
        ];
    }
}