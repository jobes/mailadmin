<?php
namespace OCA\MailAdmin\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\Mapper;

class DomainAliasMapper extends Mapper {

    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'mailadmin_domain_alias', '\OCA\MailAdmin\Db\DomainAlias');
    }

    public function findAlias($domain) {
        $sql = 'SELECT * FROM *PREFIX*mailadmin_domain_alias WHERE domain = ? ORDER BY alias';
        return $this->findEntities($sql, [$domain]);
    }

    public function delete($alias) {
        $sql = 'DELETE FROM `' . $this->tableName . '` WHERE `alias` = ?';
        $stmt = $this->execute($sql, [$alias]);
        $stmt->closeCursor();
        return $entity;
    }

}