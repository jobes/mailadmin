<?php
namespace OCA\MailAdmin\Controller;

use OCP\IL10N;
use OCP\IConfig;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;

class PageController extends Controller {
	private $userId;
	/** @var IConfig */
	private $config;
	/** @var IURLGenerator */
	private $urlGenerator;
	/** @var IL10N */
	private $l10n;
    private $cloudAddress;
    private $AppName;

	public function __construct(IRequest $request, $UserId, $AppName, IConfig $config, IURLGenerator $urlGenerator, IL10N $l10n){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->config = $config;
		$this->urlGenerator = $urlGenerator;
        $this->l10n = $l10n;
        $this->AppName = $AppName;
	}

	/**
	 * Display the navigation page of the Mailadmin app.
	 *
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 * @NoSubAdminRequired
	 *
	 * @param string $path
	 *
	 * @return TemplateResponse
	 */
	public function index(string $path = ''): TemplateResponse {
		$data = [
			'serverData' => [
				'public'   => false,
				'firstrun' => false,
				'setup'    => false,
				'isAdmin'  => \OC::$server->getGroupManager()->isAdmin($this->userId),
				'cliUrl'   => $this->getCliUrl()
			]
		];

		if($this->cloudAddress) {
			$data['serverData']['cloudAddress'] = $this->cloudAddress;
		} else {
			$cloudAddress = $this->setupCloudAddress();
			if ($cloudAddress !== ''){
				$data['serverData']['cloudAddress'] = $cloudAddress;
			} else {
				$data['serverData']['setup'] = true;

				if ($data['serverData']['isAdmin']) {
					$cloudAddress = $this->request->getParam('cloudAddress');
					if ($cloudAddress !== null) {
						$this->cloudAddress=$cloudAddress;
					} else {
						return new TemplateResponse($this->AppName, 'main', $data);
					}
				}
			}
		}

		return new TemplateResponse($this->AppName, 'main', $data);
	}

	private function setupCloudAddress(): string {
		$frontControllerActive = ($this->config->getSystemValue('htaccess.IgnoreFrontController', false) === true || getenv('front_controller_active') === 'true');

		$cloudAddress = rtrim($this->config->getSystemValue('overwrite.cli.url', ''), '/');
		if ($cloudAddress !== '') {
			if (!$frontControllerActive) {
				$cloudAddress .= '/index.php';
			}
			$this->cloudAddress = $cloudAddress;
			return $cloudAddress;
		}
		return '';
	}

	private function getCliUrl() {
		$url = rtrim($this->urlGenerator->getBaseUrl(), '/');
		$frontControllerActive = ($this->config->getSystemValue('htaccess.IgnoreFrontController', false) === true || getenv('front_controller_active') === 'true');
		if (!$frontControllerActive) {
			$url .= '/index.php';
		}
		return $url;
	}

}
