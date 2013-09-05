<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Controller context
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Backend_Controller_Context extends Magento_Core_Controller_Varien_Action_Context
{
    /**
     * @var Magento_Backend_Model_Session
     */
    protected $_session;

    /**
     * @var Magento_Backend_Helper_Data
     */
    protected $_helper;

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var Magento_Core_Model_Translate
     */
    protected $_translator;

    /**
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Core_Controller_Response_Http $response
     * @param \Magento\ObjectManager $objectManager
     * @param Magento_Core_Controller_Varien_Front $frontController
     * @param Magento_Core_Model_Layout $layout
     * @param Magento_Backend_Model_Session $session
     * @param Magento_Backend_Helper_Data $helper
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param \Magento\AuthorizationInterface $authorization
     * @param Magento_Core_Model_Translate $translator
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Controller_Request_Http $request,
        Magento_Core_Controller_Response_Http $response,
        \Magento\ObjectManager $objectManager,
        Magento_Core_Controller_Varien_Front $frontController,
        Magento_Core_Model_Layout $layout,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Backend_Model_Session $session,
        Magento_Backend_Helper_Data $helper,
        \Magento\AuthorizationInterface $authorization,
        Magento_Core_Model_Translate $translator
    ) {
        parent::__construct($request, $response, $objectManager, $frontController, $layout, $eventManager);
        $this->_session = $session;
        $this->_helper = $helper;
        $this->_authorization = $authorization;
        $this->_translator = $translator;
    }

    /**
     * @return \Magento_Backend_Helper_Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @return \Magento_Backend_Model_Session
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * @return \Magento\AuthorizationInterface
     */
    public function getAuthorization()
    {
        return $this->_authorization;
    }

    /**
     * @return \Magento_Core_Model_Translate
     */
    public function getTranslator()
    {
        return $this->_translator;
    }
}
