<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Controller_Context implements Magento_ObjectManager_ContextInterface
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var Mage_Core_Controller_Response_Http
     */
    protected $_response;

    /**
     * @var  Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Controller_Varien_Front
     */
    protected $_frontController;

    /**
     * @var Mage_Core_Model_Layout_Factory
     */
    protected $_layoutFactory;

    /**
     * @var Mage_Backend_Model_Session
     */
    protected $_session;

    /**
     * @var Mage_Backend_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @var Mage_Core_Model_Authorization
     */
    protected $_authorization;

    /**
     * @var Mage_Core_Model_Translate
     */
    protected $_translator;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param Mage_Backend_Model_Session $session
     * @param Mage_Backend_Helper_Data $helper
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Core_Model_Authorization $authorization
     * @param Mage_Core_Model_Translate $translator
     */
    public function __construct(Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        Mage_Backend_Model_Session $session,
        Mage_Backend_Helper_Data $helper,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Core_Model_Authorization $authorization,
        Mage_Core_Model_Translate $translator
    ) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_objectManager = $objectManager;
        $this->_frontController = $frontController;
        $this->_layoutFactory = $layoutFactory;
        $this->_session = $session;
        $this->_helper = $helper;
        $this->_eventManager = $eventManager;
        $this->_authorization = $authorization;
        $this->_translator = $translator;
    }

    /**
     * @return \Mage_Core_Controller_Varien_Front
     */
    public function getFrontController()
    {
        return $this->_frontController;
    }

    /**
     * @return \Mage_Core_Model_Layout_Factory
     */
    public function getLayoutFactory()
    {
        return $this->_layoutFactory;
    }

    /**
     * @return \Magento_ObjectManager
     */
    public function getObjectManager()
    {
        return $this->_objectManager;
    }

    /**
     * @return \Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return \Mage_Core_Controller_Response_Http
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @return \Mage_Backend_Helper_Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @return \Mage_Backend_Model_Session
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * @return \Mage_Core_Model_Event_Manager
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return \Mage_Core_Model_Authorization
     */
    public function getAuthorization()
    {
        return $this->_authorization;
    }

    /**
     * @return \Mage_Core_Model_Translate
     */
    public function getTranslator()
    {
        return $this->_translator;
    }
}
