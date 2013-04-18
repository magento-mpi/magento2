<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Acl
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ACL tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_Acl_CatalogManageProductTest extends Core_Mage_Acl_CatalogManageProductTest
{
    /**
     * DataProvider for product types, which can be excluded
     * Overide to exclude 'downloadable' case
     */
    public function deleteSingleProductDataProvider()
    {
        return array(
            array('simple'),
            array('virtual'),
            array('grouped'),
            array('bundle')
        );
    }
}
