<?php
namespace OCA\MailAdmin\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\Mapper;

class UserMapper extends Mapper {

    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'mailadmin_user', '\OCA\MailAdmin\Db\User');
    }

    public function findUsers($domain) {
        $sql = 'SELECT uid FROM *PREFIX*group_user LEFT JOIN *PREFIX*mailadmin_domain_group 
        ON *PREFIX*group_user.gid = *PREFIX*mailadmin_domain_group.gid
        WHERE *PREFIX*mailadmin_domain_group.domain = ? GROUP BY uid';
        return $this->findEntities($sql, [$domain]);
    }
}