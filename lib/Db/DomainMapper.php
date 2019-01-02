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
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare('DELETE FROM `' . $this->tableName . '` WHERE `domain` = ?');
            $stmt->execute([$domain]);
            
            $stmt = $this->db->prepare('DELETE FROM `*PREFIX*mailadmin_domain_group` WHERE `domain` = ?');
            $stmt->execute([$domain]);

            $this->db->commit();
        } catch (Exception $e){
            $this->db->rollback();
            throw $e;
        }

        $sql = 'DELETE FROM `' . $this->tableName . '` WHERE `domain` = ?';
        $stmt = $this->execute($sql, [$domain]);
        $stmt->closeCursor();
        return $entity;
    }

}