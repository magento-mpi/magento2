<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_User_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Helper_Data
     */
    protected $_helper;

    protected function setUp()
    {
        parent::setUp();
        $this->_helper = Mage::helper('Mage_User_Helper_Data');
    }

    /**
     * Test generate unique token for reset password confirmation link
     *
     * @covers Mage_User_Helper_Data::generateResetPasswordLinkToken
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
        $this->assertEquals(
            1,
            (int) Mage::getConfig()->getValue(
                Mage_User_Helper_Data::XML_PATH_ADMIN_RESET_PASSWORD_LINK_EXPIRATION_PERIOD,
                'default'
            )
        );
    }
}
