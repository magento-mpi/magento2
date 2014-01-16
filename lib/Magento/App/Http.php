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

use Magento\Config\Scope,
    Magento\App\ObjectManager\ConfigLoader,
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
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Config\Scope
     */
    protected $_configScope;

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
     * @param \Magento\ObjectManager $objectManager
     * @param Event\Manager $eventManager
     * @param AreaList $areaList
     * @param RequestInterface $request
     * @param Scope $configScope
     * @param ConfigLoader $configLoader
     * @param State $state
     * @param \Magento\Filesystem $filesystem
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        Event\Manager $eventManager,
        AreaList $areaList,
        RequestInterface $request,
        Scope $configScope,
        ConfigLoader $configLoader,
        State $state,
        \Magento\Filesystem $filesystem
    ) {
        $this->_objectManager = $objectManager;
        $this->_eventManager = $eventManager;
        $this->_areaList = $areaList;
        $this->_request = $request;
        $this->_configScope = $configScope;
        $this->_configLoader = $configLoader;
        $this->_state = $state;
        $this->_filesystem = $filesystem;
    }

    /**
     * Execute application
     */
    public function execute()
    {
        try {
            $areaCode = $this->_areaList->getCodeByFrontName($this->_request->getFrontName());
            $this->_state->setAreaCode($areaCode);
            $this->_objectManager->configure($this->_configLoader->load($areaCode));
            $response = $this->_objectManager->get('Magento\App\FrontControllerInterface')->dispatch($this->_request);
            // This event gives possibility to launch something before sending output (allow cookie setting)
            $eventParams = array('request' => $this->_request, 'response' => $response);
            $this->_eventManager->dispatch('controller_front_send_response_before', $eventParams);
            \Magento\Profiler::start('send_response');
            $response->sendResponse();
            \Magento\Profiler::stop('send_response');
            $this->_eventManager->dispatch('controller_front_send_response_after', $eventParams);
            return 0;
        } catch(\Exception $exception) {
            echo $exception->getMessage() . "\n";
            try {
                if ($this->_state->getMode() == State::MODE_DEVELOPER) {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                    print '<pre>';
                    print $exception->getMessage() . "\n\n";
                    print $exception->getTraceAsString();
                    print '</pre>';
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
                }
            } catch (\Exception $exception) {
                $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP 1.1';
                header($protocol . ' 500 Internal Server Error', true, 500);
                print "Unknown error happened.";
            }
            return -1;
        }
    }
}
