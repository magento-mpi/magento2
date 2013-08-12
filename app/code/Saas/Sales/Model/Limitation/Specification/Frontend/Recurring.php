<?php
/**
 * Functionality limitation checker
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Sales_Model_Limitation_Specification_Frontend_Recurring
    implements Saas_Saas_Model_Limitation_SpecificationInterface
{
    /**
     * Check is allowed functionality for the module
     *
     * @param Magento_Core_Controller_Request_Http $request
     * @return bool
     */
    public function isSatisfiedBy(Magento_Core_Controller_Request_Http $request)
    {
        return $request->getControllerName() != 'recurring_profile'
            || $request->getControllerModule() != 'Magento_Sales';
    }
}
