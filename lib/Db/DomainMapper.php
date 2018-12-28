<?php
namespace OCA\MailAdmin\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\Mapper;

class DomainMapper extends Mapper {

    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'mailadmin_domains', '\OCA\MailAdmin\Db\Domain');
    }

    public function findAll() {
        $sql = 'SELECT * FROM *PREFIX*mailadmin_domains ORDER BY domain';
        return $this->findEntities($sql, []);
    }

    public function delete($domain) {
        $sql = 'DELETE FROM `' . $this->tableName . '` WHERE `domain` = ?';
        $stmt = $this->execute($sql, [$domain]);
        $stmt->closeCursor();
        return $entity;
    }

}