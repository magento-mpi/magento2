<?php
/**
 * Specification interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Saas_Saas_Model_Limitation_SpecificationInterface
{
    /**
     * Check is allowed functionality for the module
     *
     * @param Magento_Core_Controller_Request_Http $request
     * @return bool
     */
    public function isSatisfiedBy(Magento_Core_Controller_Request_Http $request);
}
