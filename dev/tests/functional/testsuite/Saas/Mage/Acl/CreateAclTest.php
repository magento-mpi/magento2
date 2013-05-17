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
class Saas_Mage_Acl_CreateAclTest extends Core_Mage_Acl_CreateAclTest
{
    /*
     * Override Saas specific pages
     */
    public function roleResourceAccessDataProvider()
    {
        return array(
            array('external_page_cache', 'access_denied', 0 ,0),
            array('dashboard', 'access_denied', 0 ,0),
            array('global_search', 'access_denied', 0 ,1)
        );
    }
}
