<?php
/**
 * Functionality limitation checker
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Newsletter_Model_Limitation_Specification_Backend_Newsletter
    implements Saas_Saas_Model_Limitation_SpecificationInterface
{
    /**
     * Check is allowed functionality for the module
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return bool
     */
    public function isSatisfiedBy(Mage_Core_Controller_Request_Http $request)
    {
        if ('Magento_Adminhtml' == $request->getControllerModule()) {
            $controllerParts = explode('_', $request->getControllerName());

            if ('newsletter' == $controllerParts[0]
                && (isset($controllerParts[1]) && 'subscriber' != $controllerParts[1])
            ) {
                return false;
            }
        }
        return true;
    }
}
