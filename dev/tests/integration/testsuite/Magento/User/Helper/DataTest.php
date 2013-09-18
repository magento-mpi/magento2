<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_User_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helper;

    protected function setUp()
    {
        parent::setUp();
        $this->_helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\User\Helper\Data');
    }

    /**
     * Test generate unique token for reset password confirmation link
     *
     * @covers \Magento\User\Helper\Data::generateResetPasswordLinkToken
     */
    public function testGenerateResetPasswordLinkToken()
    {
        $actual = $this->_helper->generateResetPasswordLinkToken();
        $this->assertGreaterThan(15, strlen($actual));
    }

    /**
     * Test retrieve customer reset password link expiration period in days
     *
     */
    public function testGetResetPasswordLinkExpirationPeriod()
    {
        /** @var $configModel Magento_Core_Model_Config */
        $configModel = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Config');
        $this->assertEquals(
            1,
            (int) $configModel->getValue(
                Magento_User_Helper_Data::XML_PATH_ADMIN_RESET_PASSWORD_LINK_EXPIRATION_PERIOD,
                'default'
        ));
    }
}
