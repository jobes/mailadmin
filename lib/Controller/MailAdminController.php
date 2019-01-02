<?php
namespace OCA\MailAdmin\Controller;

use Exception;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\MailAdmin\Db\Domain;
use OCA\MailAdmin\Db\UserAlias;
use OCA\MailAdmin\Db\DomainMapper;
use OCA\MailAdmin\Db\GroupMapper;
use OCA\MailAdmin\Db\DomainGroupMapper;
use OCA\MailAdmin\Db\UserMapper;
use OCA\MailAdmin\Db\UserAliasMapper;

use \OCP\ILogger;

class MailAdminController extends Controller {

   private $service;
   private $userId;
   private $logger;

   private $domainMapper;
   private $groupMapper;
   private $domainGroupMapper;
   private $userMapper;
   private $userAliasMapper;
     

   use Errors;

   public function __construct($AppName, 
                              IRequest $request, 
                              $UserId, 
                              ILogger $logger, 
                              DomainMapper $domainMapper, 
                              GroupMapper $groupMapper,
                              DomainGroupMapper $domainGroupMapper,
                              UserMapper $userMapper,
                              UserAliasMapper $userAliasMapper
                              ){
      parent::__construct($AppName, $request);
      $this->service = $service;
      $this->userId = $UserId;
      $this->logger = $logger;
      $this->domainMapper = $domainMapper;
      $this->groupMapper = $groupMapper;
      $this->domainGroupMapper = $domainGroupMapper;
      $this->userMapper = $userMapper;
      $this->userAliasMapper = $userAliasMapper;
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
    * @param string $added
    * @param string $removed
    */
   public function domainGroupUpdate($domain, $added, $removed) {
      return $this->domainGroupMapper->update($domain, $added, $removed);
   }

   /**
    * @param string $domain
    */
   public function getUsers($domain) {
      return $this->userMapper->findUsers($domain);
   }
   
   /**
    * @param string $email
    */
   public function getUserAlias($email) {
      return $this->userAliasMapper->findUsers($email);
   }

   /**
    * @param string $email
    * @param string $alias
    */
   public function deleteUserAlias($email, $alias) {
      return $this->userAliasMapper->delete($alias, $email);
   }

   /**
    * @param string $email
    * @param string $alias
    * @param string $validto
   */
  public function createUserAlias($email, $alias, $validto) {
   $userAliasObj = new UserAlias();
   $userAliasObj->setEmail($email);
   $userAliasObj->setAlias($alias);
   $userAliasObj->setValidto($validto);
   return $this->userAliasMapper->insert($userAliasObj);
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