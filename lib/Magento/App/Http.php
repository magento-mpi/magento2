<?php
/**
 * Http application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

use Magento\App\ObjectManager\ConfigLoader,
    Magento\Event;

class Http implements \Magento\AppInterface
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Event\Manager
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
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var Response\Http
     */
    protected $_response;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param Event\Manager $eventManager
     * @param AreaList $areaList
     * @param Request\Http $request
     * @param Response\Http $response
     * @param ConfigLoader $configLoader
     * @param State $state
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        Event\Manager $eventManager,
        AreaList $areaList,
        \Magento\App\Request\Http $request,
        \Magento\App\Response\Http $response,
        ConfigLoader $configLoader,
        State $state,
        \Magento\Filesystem $filesystem
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
     * @return ResponseInterfaceшт
     */
    public function execute()
    {
        try {
            $areaCode = $this->_areaList->getCodeByFrontName($this->_request->getFrontName());
            $this->_state->setAreaCode($areaCode);
            $this->_objectManager->configure($this->_configLoader->load($areaCode));
            $this->_response = $this->_objectManager
                ->get('Magento\App\FrontControllerInterface')
                ->dispatch($this->_request);
            // This event gives possibility to launch something before sending output (allow cookie setting)
            $eventParams = array('request' => $this->_request, 'response' => $this->_response);
            $this->_eventManager->dispatch('controller_front_send_response_before', $eventParams);
        } catch(\Exception $exception) {
            $message = $exception->getMessage() . "\n";
            try {
                if ($this->_state->getMode() == State::MODE_DEVELOPER) {
                    $message .= '<pre>';
                    $message .= $exception->getMessage() . "\n\n";
                    $message .= $exception->getTraceAsString();
                    $message .= '</pre>';
                } else {
                    $reportData = array($exception->getMessage(), $exception->getTraceAsString());
                    // retrieve server data
                    if (isset($_SERVER)) {
                        if (isset($_SERVER['REQUEST_URI'])) {
                            $reportData['url'] = $_SERVER['REQUEST_URI'];
                        }
                        if (isset($_SERVER['SCRIPT_NAME'])) {
                            $reportData['script_name'] = $_SERVER['SCRIPT_NAME'];
                        }
                    }
                    require_once ($this->_filesystem->getPath(\Magento\Filesystem::PUB) . '/errors/report.php');
                    $processor = new \Error_Processor($this->_response);
                    $processor->saveReport($reportData);
                    $this->_response = $processor->processReport();
                }
            } catch (\Exception $exception) {
                $message .= "Unknown error happened.";
            }
            $this->_response->setHttpResponseCode(500);
            $this->_response->setBody($message);
        }
        return $this->_response;
    }
}
