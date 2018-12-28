<?php
namespace OCA\MailAdmin\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Group extends Entity implements JsonSerializable {

    protected $gid;

    public function jsonSerialize() {
        return [
            'gid' => $this->gid
        ];
    }
}