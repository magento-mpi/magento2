<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Controller_Varien_Action_Context implements Magento_ObjectManager_ContextInterface
{
    /**
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var Magento_Core_Controller_Response_Http
     */
    protected $_response;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Controller_Varien_Front
     */
    protected $_frontController = null;

    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * Should inherited page be rendered
     *
     * @var bool
     */
    protected $_isRenderInherited;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Controller_Varien_Front $frontController
     * @param Magento_Core_Model_Layout $layout
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param $isRenderInherited
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Controller_Request_Http $request,
        Magento_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Magento_Core_Controller_Varien_Front $frontController,
        Magento_Core_Model_Layout $layout,
        Magento_Core_Model_Event_Manager $eventManager,
        $isRenderInherited
    ) {
        $this->_request           = $request;
        $this->_response          = $response;
        $this->_objectManager     = $objectManager;
        $this->_frontController   = $frontController;
        $this->_layout            = $layout;
        $this->_eventManager      = $eventManager;
        $this->_isRenderInherited = $isRenderInherited;
        $this->_logger            = $logger;
    }

    /**
     * Should inherited page be rendered
     *
     * @return bool
     */
    public function isRenderInherited()
    {
        return $this->_isRenderInherited;
    }

    /**
     * @return \Magento_Core_Controller_Varien_Front
     */
    public function getFrontController()
    {
        return $this->_frontController;
    }

    /**
     * @return \Magento_Core_Model_Layout
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * @return \Magento_ObjectManager
     */
    public function getObjectManager()
    {
        return $this->_objectManager;
    }

    /**
     * @return \Magento_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return \Magento_Core_Controller_Response_Http
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @return \Magento_Core_Model_Event_Manager
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return Magento_Core_Model_Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }
}
