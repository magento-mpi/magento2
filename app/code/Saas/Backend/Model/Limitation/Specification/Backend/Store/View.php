<?php
/**
 * Functionality limitation checker
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Backend_Model_Limitation_Specification_Backend_Store_View
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
        return $request->getControllerName() != 'system_store'
            || $request->getControllerModule() != 'Saas_Backend_Adminhtml'
            || $request->getActionName() != 'deleteStore';
    }
}
