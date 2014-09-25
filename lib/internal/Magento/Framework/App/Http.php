<?php
/**
 * Http application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

use Magento\Framework\App\ObjectManager\ConfigLoader;
use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\Event;
use Magento\Framework\Controller\ResultInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Http implements \Magento\Framework\AppInterface
{
    /**#@+
     * Parameters for redirecting if the application is not installed
     */
    const NOT_INSTALLED_URL_PATH_PARAM = 'MAGE_NOT_INSTALLED_URL_PATH';
    const NOT_INSTALLED_URL_PARAM = 'MAGE_NOT_INSTALLED_URL';
    const NOT_INSTALLED_URL_PATH = 'setup/';
    /**#@-*/

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Event\Manager
     */
    protected $_eventManager;

    /**
     * @var AreaList
     */
    protected $_areaList;

    /**
     * @var Request\Http
     */
    protected $_request;

    /**
     * @var ConfigLoader
     */
    protected $_configLoader;

    /**
     * @var State
     */
    protected $_state;

    /**
     * @var Filesystem
     */
    protected $_filesystem;

    /**
     * @var ResponseHttp
     */
    protected $_response;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param Event\Manager $eventManager
     * @param AreaList $areaList
     * @param RequestHttp $request
     * @param ResponseHttp $response
     * @param ConfigLoader $configLoader
     * @param State $state
     * @param Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        Event\Manager $eventManager,
        AreaList $areaList,
        RequestHttp $request,
        ResponseHttp $response,
        ConfigLoader $configLoader,
        State $state,
        Filesystem $filesystem
    ) {
        $this->_objectManager = $objectManager;
        $this->_eventManager = $eventManager;
        $this->_areaList = $areaList;
        $this->_request = $request;
        $this->_response = $response;
        $this->_configLoader = $configLoader;
        $this->_state = $state;
        $this->_filesystem = $filesystem;
    }

    /**
     * Run application
     *
     * @throws \InvalidArgumentException
     * @return ResponseInterface
     */
    public function launch()
    {
        $areaCode = $this->_areaList->getCodeByFrontName($this->_request->getFrontName());
        $this->_state->setAreaCode($areaCode);
        $this->_objectManager->configure($this->_configLoader->load($areaCode));
        $result = $this->_objectManager->get('Magento\Framework\App\FrontControllerInterface')
            ->dispatch($this->_request);
        // TODO: Temporary solution till all controllers are returned not ResultInterface (MAGETWO-28359)
        if ($result instanceof ResultInterface) {
            $result->renderResult($this->_response);
        } elseif ($result instanceof ResponseHttp) {
            $this->_response = $result;
        } else {
            throw new \InvalidArgumentException('Invalid return type');
        }
        // This event gives possibility to launch something before sending output (allow cookie setting)
        $eventParams = array('request' => $this->_request, 'response' => $this->_response);
        $this->_eventManager->dispatch('controller_front_send_response_before', $eventParams);
        return $this->_response;
    }

    /**
     * {@inheritdoc}
     */
    public function catchException(Bootstrap $bootstrap, \Exception $exception)
    {
        $result = $this->handleDeveloperMode($bootstrap, $exception)
            || $this->handleBootstrapErrors($bootstrap)
            || $this->handleSessionException($bootstrap, $exception)
            || $this->handleInitException($exception)
            || $this->handleGenericReport($bootstrap, $exception);
        return $result;
    }

    /**
     * Error handler for developer mode
     *
     * @param Bootstrap $bootstrap
     * @param \Exception $exception
     * @return bool
     */
    private function handleDeveloperMode(Bootstrap $bootstrap, \Exception $exception)
    {
        if ($bootstrap->isDeveloperMode()) {
            $this->_response->setHttpResponseCode(500);
            $this->_response->setHeader('Content-Type', 'text/plain');
            $this->_response->setBody($exception->getMessage() . "\n" . $exception->getTraceAsString());
            $this->_response->sendResponse();
            return true;
        }
        return false;
    }

    /**
     * Handler for bootstrap errors
     *
     * @param Bootstrap $bootstrap
     * @return bool
     */
    private function handleBootstrapErrors(Bootstrap $bootstrap)
    {
        $bootstrapCode = $bootstrap->getErrorCode();
        if (Bootstrap::ERR_MAINTENANCE == $bootstrapCode) {
            require $this->_filesystem->getPath(Filesystem::PUB_DIR) . '/errors/503.php';
            return true;
        }
        if (Bootstrap::ERR_IS_INSTALLED == $bootstrapCode) {
            $path = $this->getInstallerRedirectPath($bootstrap->getParams());
            $this->_response->setRedirect($path);
            $this->_response->sendHeaders();
            return true;
        }
        return false;
    }

    /**
     * Handler for session errors
     *
     * @param Bootstrap $bootstrap
     * @param \Exception $exception
     * @return bool
     */
    private function handleSessionException(Bootstrap $bootstrap, \Exception $exception)
    {
        if ($exception instanceof \Magento\Framework\Session\Exception) {
            $path = $this->getBaseUrlPath($bootstrap->getParams());
            $this->_response->setRedirect($path);
            $this->_response->sendHeaders();
            return true;
        }
        return false;
    }

    /**
     * Handler for application initialization errors
     *
     * @param \Exception $exception
     * @return bool
     */
    private function handleInitException(\Exception $exception)
    {
        if ($exception instanceof \Magento\Framework\App\InitException) {
            require $this->_filesystem->getPath(Filesystem::PUB_DIR) . '/errors/404.php';
            return true;
        }
        return false;
    }

    /**
     * Handle for any other errors
     *
     * @param Bootstrap $bootstrap
     * @param \Exception $exception
     * @return bool
     */
    private function handleGenericReport(Bootstrap $bootstrap, \Exception $exception)
    {
        $reportData = array($exception->getMessage(), $exception->getTraceAsString());
        $params = $bootstrap->getParams();
        if (isset($params['REQUEST_URI'])) {
            $reportData['url'] = $params['REQUEST_URI'];
        }
        if (isset($params['SCRIPT_NAME'])) {
            $reportData['script_name'] = $params['SCRIPT_NAME'];
        }
        require $this->_filesystem->getPath(Filesystem::PUB_DIR) . '/errors/report.php';
        return true;
    }

    /**
     * Determines redirect URL when application is not installed
     *
     * @param array $server
     * @return string
     */
    public function getInstallerRedirectPath($server)
    {
        if (isset($server[self::NOT_INSTALLED_URL_PARAM])) {
            return $server[self::NOT_INSTALLED_URL_PARAM];
        }
        if (isset($server[self::NOT_INSTALLED_URL_PATH_PARAM])) {
            $urlPath = $server[self::NOT_INSTALLED_URL_PATH_PARAM];
        } else {
            $urlPath = self::NOT_INSTALLED_URL_PATH;
        }
        return $this->getBaseUrlPath($server) . $urlPath;
    }

    /**
     * Determines a base URL path from the environment
     *
     * @param string $server
     * @return string
     */
    private function getBaseUrlPath($server)
    {
        $result = '';
        if (isset($server['SCRIPT_NAME'])) {
            $envPath = str_replace('\\', '/', dirname($server['SCRIPT_NAME']));
            if ($envPath != '.' && $envPath != '/') {
                $result = $envPath;
            }
        }
        if (!preg_match('/\/$/', $result)) {
            $result .= '/';
        }
        return $result;
    }
}
