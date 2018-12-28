<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\MailAdmin\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
       ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
       
       ['name' => 'MailAdmin#allDomain', 'url' => '/domains', 'verb' => 'GET'],
       ['name' => 'MailAdmin#createDomain', 'url' => '/domain', 'verb' => 'POST'],
       ['name' => 'MailAdmin#deleteDomain', 'url' => '/domains/{domain}', 'verb' => 'DELETE']
    ]
];
