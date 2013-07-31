<?php
/**
 * Functionality limitation checker
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Design_Model_Limitation_Specification_Backend_Schedule
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
        return $request->getControllerName() != 'system_design' || $request->getControllerModule() != 'Magento_Adminhtml';
    }
}
