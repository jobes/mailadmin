<?php
namespace OCA\MailAdmin\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class DomainGroup extends Entity implements JsonSerializable {

    protected $gid;
    protected $domain;

    public function jsonSerialize() {
        return [
            'gid' => $this->gid,
            'domain' => $this->domain
        ];
    }
}