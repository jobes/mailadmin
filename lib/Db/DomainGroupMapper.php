<?php
namespace OCA\MailAdmin\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\Mapper;
use \OCP\ILogger;
class DomainGroupMapper extends Mapper {
    private $logger;
    public function __construct(IDbConnection $db, ILogger $logger) {
        parent::__construct($db, 'mailadmin_domain_group', '\OCA\MailAdmin\Db\DomainGroup');
        $this->logger = $logger;
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

    public function update($domain, $added, $removed) {
        $this->logger->warning("--------------------");
        try {
            $this->db->beginTransaction();
            for ($i = 0; $i < count($added); $i++) {
                $stmt = $this->db->prepare('INSERT INTO `' . $this->tableName . '` (`DOMAIN`, `GID`) VALUES (?, ?); ');
                $stmt->execute([$domain, $added[$i]]);
            }
    
            for ($i = 0; $i < count($removed); $i++) {
                $stmt = $this->db->prepare('DELETE FROM `' . $this->tableName . '` WHERE `domain` = ? AND `gid` = ?;');
                $stmt->execute([$domain, $removed[$i]]);
            }
            $this->db->commit();
        } catch (Exception $e){
            $this->db->rollback();
            throw $e;
        }
        
    }

}