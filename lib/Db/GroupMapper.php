<?php
namespace OCA\MailAdmin\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\Mapper;

class GroupMapper extends Mapper {

    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'groups', '\OCA\MailAdmin\Db\Group');
    }

    public function find($gid) {
        $sql = 'SELECT * FROM *PREFIX*groups WHERE gid = ?';
        return $this->findEntity($sql, [$gid]);
    }

    public function findAll() {
        $sql = 'SELECT * FROM *PREFIX*groups ORDER BY gid';
        return $this->findEntities($sql, []);
    }

}