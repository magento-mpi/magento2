<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Controller context
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Backend_Controller_Context extends Magento_Core_Controller_Varien_Action_Context
{
    /**
     * @var Mage_Backend_Model_Session
     */
    protected $_session;

    /**
     * @var Mage_Backend_Helper_Data
     */
    protected $_helper;

    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var Magento_Core_Model_Translate
     */
    protected $_translator;

    /**
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Controller_Varien_Front $frontController
     * @param Magento_Core_Model_Layout $layout
     * @param Mage_Backend_Model_Session $session
     * @param Mage_Backend_Helper_Data $helper
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_AuthorizationInterface $authorization
     * @param Magento_Core_Model_Translate $translator
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Controller_Request_Http $request,
        Magento_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Magento_Core_Controller_Varien_Front $frontController,
        Magento_Core_Model_Layout $layout,
        Magento_Core_Model_Event_Manager $eventManager,
        Mage_Backend_Model_Session $session,
        Mage_Backend_Helper_Data $helper,
        Magento_AuthorizationInterface $authorization,
        Magento_Core_Model_Translate $translator
    ) {
        parent::__construct($request, $response, $objectManager, $frontController, $layout, $eventManager);
        $this->_session = $session;
        $this->_helper = $helper;
        $this->_authorization = $authorization;
        $this->_translator = $translator;
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
     * @return \Magento_AuthorizationInterface
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
