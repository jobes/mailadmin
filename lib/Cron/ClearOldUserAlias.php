<?php
namespace OCA\MailAdmin\Cron;

use \OCP\ILogger;
use OCP\IDbConnection;
use OC\BackgroundJob\TimedJob;
use OCA\MailAdmin\Db\UserAliasMapper;

class ClearOldUserAlias extends TimedJob {
    private $userAliasMapper;

    public function __construct(UserAliasMapper $userAliasMapper) {
        $this->userAliasMapper = $userAliasMapper;
        $this->setInterval(60*60); //per hour
    }

    protected function run($argument) {
        $this->userAliasMapper->deleteAllOldAlias();
    }

}