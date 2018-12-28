<?php
namespace OCA\MailAdmin\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\Mapper;

class DomainGroupMapper extends Mapper {

    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'mailadmin_domain_group', '\OCA\MailAdmin\Db\DomainGroup');
    }

    public function findDomain($domain) {
        $sql = 'SELECT * FROM *PREFIX*mailadmin_domain_group WHERE domain = ?';
        return $this->findEntities($sql, [$domain]);
    }

    public function delete($gid, $domain) {
        $sql = 'DELETE FROM `' . $this->tableName . '` WHERE `domain` = ? AND `gid` = ?';
        $stmt = $this->execute($sql, [$domain, $gid]);
        $stmt->closeCursor();
        return $entity;
    }

}