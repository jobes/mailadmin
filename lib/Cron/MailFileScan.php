<?php
namespace OCA\MailAdmin\Cron;

use \OCP\ILogger;
use OCP\IDbConnection;
use OC\BackgroundJob\TimedJob;
use OCA\MailAdmin\Db\UserMapper;

class MailFileScan extends TimedJob {
    private $userMapper;
    private $logger;
    private $db;
    private $domainMapper;

    public function __construct(UserMapper $userMapper, ILogger $logger, IDbConnection $db) {
        $this->userMapper = $userMapper;
        $this->logger = $logger;
        $this->db = $db;

        $this->setInterval(60*60); //per hour
    }

    protected function run($argument) {
        $users = $this->userMapper->findAllUsers();
        foreach ($users as $user) {
            try {
                $scanner = new \OC\Files\Utils\Scanner($user->getUid(), $this->db, $this->logger);
                $scanner->scan($user->getUid() . "/files/.mail");
            } catch (Exception $e) {
                $this->logger->warning($e->getMessage());
            }
            
        }
    }

}