<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import Export Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Magento_ImportExport_Helper extends Core_Magento_ImportExport_Helper
{
    /**
     * Get list of Customer Entity Types specific for Magento versions
     *
     * @return array
     */
    public function getCustomerEntityType()
    {
        return array('Customers Main File', 'Customer Addresses', 'Customer Finances');
    }
}
