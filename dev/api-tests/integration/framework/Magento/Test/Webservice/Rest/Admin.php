<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_Webservice_Rest_Admin extends Magento_Test_Webservice_Rest_Abstract
{
    protected $_userType = 'admin';

    /**
     * Prepare ACL
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        require dirname(__FILE__) . '/../../../../../fixture/Acl/admin_acl.php';
    }

    /**
     * Delete acl fixture after test case
     */
    public static function tearDownAfterClass()
    {
        Magento_TestCase::deleteFixture('role', true);
        Magento_TestCase::deleteFixture('rule', true);
        Magento_TestCase::deleteFixture('attribute', true);
        Magento_Test_Webservice::setFixture('admin_acl_is_prepared', false);

        parent::tearDownAfterClass();
    }
}
