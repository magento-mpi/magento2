<?php
/**
 * Newsletter observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Newsletter_Model_Observer
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var Saas_Saas_Helper_Data
     */
    protected $_saasHelper;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Saas_Saas_Helper_Data $saasHelper
     */
    public function __construct(Mage_Core_Controller_Request_Http $request, Saas_Saas_Helper_Data $saasHelper)
    {
        $this->_request = $request;
        $this->_saasHelper = $saasHelper;
    }

    /**
     * Limit newsletter module functionality
     *
     * @param Varien_Event_Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function limitNewsletterFunctionality(Varien_Event_Observer $observer)
    {
        if ('Mage_Adminhtml' == $this->_request->getControllerModule()) {
            $controllerParts = explode('_', $this->_request->getControllerName());

            if ('newsletter' == $controllerParts[0]
                && (isset($controllerParts[1]) && 'subscriber' != $controllerParts[1])
            ) {
                $this->_saasHelper->customizeNoRoutForward($this->_request);
            }
        }
    }
}
