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
class Mage_Backend_Controller_Context extends Mage_Core_Controller_Varien_Action_Context
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
     * @var Mage_Core_Model_Translate
     */
    protected $_translator;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Backend_Model_Session $session
     * @param Mage_Backend_Helper_Data $helper
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Magento_AuthorizationInterface $authorization
     * @param Mage_Core_Model_Translate $translator
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Backend_Model_Session $session,
        Mage_Backend_Helper_Data $helper,
        Magento_AuthorizationInterface $authorization,
        Mage_Core_Model_Translate $translator
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
     * @return \Mage_Core_Model_Translate
     */
    public function getTranslator()
    {
        return $this->_translator;
    }
}
