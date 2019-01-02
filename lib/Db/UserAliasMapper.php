<?php
namespace OCA\MailAdmin\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\Mapper;

class UserAliasMapper extends Mapper {

    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'mailadmin_user_alias', '\OCA\MailAdmin\Db\UserAlias');
    }

    public function findUsers($email) {
        $sql = 'SELECT * FROM *PREFIX*mailadmin_user_alias WHERE email = ? ORDER BY alias';
        return $this->findEntities($sql, [$email]);
    }

    public function delete($alias, $email) {
        $sql = 'DELETE FROM `' . $this->tableName . '` WHERE `alias` = ? AND `email` = ?';
        $stmt = $this->execute($sql, [$alias, $email]);
        $stmt->closeCursor();
        return $entity;
    }

}