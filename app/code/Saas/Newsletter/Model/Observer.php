<?php
/**
 * Newsletter observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Newsletter_Model_Observer extends Saas_Saas_Model_Observer_Controller_LimitationAbstract
{
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
