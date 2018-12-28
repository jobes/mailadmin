<?php
namespace OCA\MailAdmin\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Domain extends Entity implements JsonSerializable {

    protected $domain;

    public function jsonSerialize() {
        return [
            'domain' => $this->domain
        ];
    }
}