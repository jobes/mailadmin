<?php
 namespace OCA\MailAdmin\Controller;

 use Exception;

 use OCP\IRequest;
 use OCP\AppFramework\Http\DataResponse;
 use OCP\AppFramework\Controller;

 use OCA\MailAdmin\Db\Domain;
 use OCA\MailAdmin\Db\DomainMapper;

 use \OCP\ILogger;

 class MailAdminController extends Controller {

     private $service;
     private $userId;
     private $logger;

     private $domainMapper;
     

     use Errors;

     public function __construct($AppName, IRequest $request, $UserId, ILogger $logger, DomainMapper $domainMapper){
         parent::__construct($AppName, $request);
         $this->service = $service;
         $this->userId = $UserId;
         $this->logger = $logger;
         $this->domainMapper = $domainMapper;
     }

     /**
      * @NoAdminRequired
      *
      * @param string $domain
      */
     public function allDomain() {
        return new DataResponse($this->domainMapper->findAll());
     }

     /**
      * @NoAdminRequired
      *
      * @param string $domain
      */
      public function createDomain($domain) {
        $domainObj = new Domain();
        $domainObj->setDomain($domain);
        return $this->domainMapper->insert($domainObj);
     }

     /**
      * @NoAdminRequired
      *
      * @param string $domain
      */
      public function deleteDomain($domain) {
        return $this->domainMapper->delete($domain);
     }

     /**
      * @NoAdminRequired
      *
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