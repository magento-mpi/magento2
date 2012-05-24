<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract class for products resource tests as guest role
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
abstract class Api2_Catalog_Products_GuestAbstract extends Api2_Catalog_Products_Abstract
{
    protected $_userType = 'guest';

    /**
     * Prepare ACL
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        require TESTS_FIXTURES_DIRECTORY . '/Acl/guest_acl.php';
    }

    /**
     * Delete acl fixture after test case
     */
    public static function tearDownAfterClass()
    {
        Magento_Test_Webservice::setFixture('guest_acl_is_prepared', false);

        parent::tearDownAfterClass();
    }
}
