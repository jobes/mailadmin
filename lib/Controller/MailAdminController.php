<?php
namespace OCA\MailAdmin\Controller;

use Exception;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\MailAdmin\Db\Domain;
use OCA\MailAdmin\Db\DomainMapper;
use OCA\MailAdmin\Db\GroupMapper;
use OCA\MailAdmin\Db\DomainGroupMapper;
use OCA\MailAdmin\Db\UserMapper;

use \OCP\ILogger;

class MailAdminController extends Controller {

    private $service;
    private $userId;
    private $logger;

    private $domainMapper;
    private $groupMapper;
    private $domainGroupMapper;
    private $userMapper;
     

    use Errors;

    public function __construct($AppName, 
                                IRequest $request, 
                                $UserId, 
                                ILogger $logger, 
                                DomainMapper $domainMapper, 
                                GroupMapper $groupMapper,
                                DomainGroupMapper $domainGroupMapper,
                                UserMapper $userMapper
                                ){
        parent::__construct($AppName, $request);
        $this->service = $service;
        $this->userId = $UserId;
        $this->logger = $logger;
        $this->domainMapper = $domainMapper;
        $this->groupMapper = $groupMapper;
        $this->domainGroupMapper = $domainGroupMapper;
        $this->userMapper = $userMapper;
    }

    /**
     * @param string $domain
     */
    public function allDomain() {
        return new DataResponse($this->domainMapper->findAll());
    }

    /**
     * @param string $domain
     */
     public function createDomain($domain) {
       $domainObj = new Domain();
       $domainObj->setDomain($domain);
       return $this->domainMapper->insert($domainObj);
    }

    /**
     * @param string $domain
     */
     public function deleteDomain($domain) {
       return $this->domainMapper->delete($domain);
    }

    /**
     */
     public function allGroups() {
       return new DataResponse($this->groupMapper->findAll());
    }

    /**
     * @param string $domain
     */
    public function getDomainGroupForDomain($domain) {
        return $this->domainGroupMapper->findDomain($domain);
    }

    /**
     * @param string $domain
     */
    public function domainGroupUpdate($domain, $added, $removed) {
        return $this->domainGroupMapper->update($domain, $added, $removed);
     }

     /**
     * @param string $domain
     */
    public function getUsers($domain) {
        $x = $this->userMapper->findUsers($domain);
        $this->logger->warning("-----------------");
        $this->logger->warning(serialize($x));
        return $x;
     }


    /**
     * @param int $id
     */
     public function show($id) {
        try {
           return new DataResponse($this->mapper->find($id, $this->userId));
        } catch(Exception $e) {
           return new DataResponse([], Http::STATUS_NOT_FOUND);
        }
    }
 }