<?php
namespace OCA\MailAdmin\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class DomainAlias extends Entity implements JsonSerializable {

    protected $alias;
    protected $domain;

    public function jsonSerialize() {
        return [
            'alias' => $this->alias,
            'domain' => $this->domain
        ];
    }
}