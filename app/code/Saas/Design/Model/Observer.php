<?php
/**
 * Observer for the Saas_Design module
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Design_Model_Observer
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    private $_request;

    /**
     * @var Saas_Saas_Helper_Data
     */
    private $_saasHelper;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Saas_Saas_Helper_Data $saasHelper
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Saas_Saas_Helper_Data $saasHelper
    ) {
        $this->_request = $request;
        $this->_saasHelper = $saasHelper;
    }

    /**
     * Limit Design Themes functionality
     *
     * @param Varien_Event_Observer $observer
     * @return Saas_Design_Model_Observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function limitThemesFunctionality(Varien_Event_Observer $observer)
    {
        if ($this->_request->getControllerName() == 'system_design_theme'
            && $this->_request->getControllerModule() == 'Mage_Theme_Adminhtml'
            && in_array($this->_request->getActionName(), array('index', 'new', 'grid', 'edit'))
        ) {
            $this->_saasHelper->customizeNoRoutForward($this->_request);
        }
        return $this;
    }

    /**
     * Disable Design Schedule functionality
     *
     * @param Varien_Event_Observer $observer
     * @return Saas_Design_Model_Observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function disableScheduleFunctionality(Varien_Event_Observer $observer)
    {
       if ($this->_request->getControllerName() == 'system_design'
            && $this->_request->getControllerModule() == 'Mage_Adminhtml'
        ) {
            $this->_saasHelper->customizeNoRoutForward($this->_request);
        }
        return $this;
    }
}
