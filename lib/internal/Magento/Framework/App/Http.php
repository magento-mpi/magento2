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

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Http implements \Magento\Framework\AppInterface
{
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
     * @return ResponseInterface
     */
    public function launch()
    {
        $areaCode = $this->_areaList->getCodeByFrontName($this->_request->getFrontName());
        $this->_state->setAreaCode($areaCode);
        $this->_objectManager->configure($this->_configLoader->load($areaCode));
        $this->_response = $this->_objectManager->get('Magento\Framework\App\FrontControllerInterface')
            ->dispatch($this->_request);
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
        switch ($bootstrap->getErrorCode()) {
            case Bootstrap::ERR_MAINTENANCE:
                require $this->_filesystem->getPath(Filesystem::PUB_DIR) . '/errors/503.php';
                return true;
            case Bootstrap::ERR_IS_INSTALLED:
                $this->_response->setRedirect('/setup/'); // TODO: remove hardcode
                $this->_response->sendHeaders();
                return true;
            default:
                if ($bootstrap->isDeveloperMode()) {
                    return false;
                }
        }
        $message = $exception->getMessage() . "\n";
        try {
            $reportData = array($exception->getMessage(), $exception->getTraceAsString());
            $params = $bootstrap->getParams();
            if (isset($params['REQUEST_URI'])) {
                $reportData['url'] = $params['REQUEST_URI'];
            }
            if (isset($params['SCRIPT_NAME'])) {
                $reportData['script_name'] = $params['SCRIPT_NAME'];
            }
            require_once $this->_filesystem->getPath(Filesystem::PUB_DIR) . '/errors/report.php';
            $processor = new \Magento\Framework\Error\Processor($this->_response);
            $processor->saveReport($reportData);
            $this->_response = $processor->processReport();
            return true;
        } catch (\Exception $exception) {
            $message .= "Unknown error happened.";
        }
        $this->_response->setHttpResponseCode(500);
        $this->_response->setBody($message);
        return true;
    }
}
