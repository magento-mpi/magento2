<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_ImportExport_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_ImportExport_Helper_Data
     */
    protected $_importExportHelper;

    /**
     * Set import/export helper
     *
     * @static
     */
    protected function setUp()
    {
        $this->_importExportHelper = Mage::helper('Enterprise_ImportExport_Helper_Data');
    }

    /**
     * Is reward points enabled in config - active/enabled
     *
     * @magentoConfigFixture               modules/Enterprise_Reward/active      1
     * @magentoConfigFixture current_store enterprise_reward/general/is_enabled  1
     */
    public function testIsRewardPointsEnabledActiveEnabled()
    {
        $this->assertTrue($this->_importExportHelper->isRewardPointsEnabled());
    }

    /**
     * Is reward points enabled in config - active/disabled
     *
     * @magentoConfigFixture               modules/Enterprise_Reward/active      1
     * @magentoConfigFixture current_store enterprise_reward/general/is_enabled  0
     */
    public function testIsRewardPointsEnabledActiveDisabled()
    {
        $this->assertFalse($this->_importExportHelper->isRewardPointsEnabled());
    }

    /**
     * Is reward points enabled in config - inactive/enabled
     *
     * @magentoConfigFixture               modules/Enterprise_Reward/active      0
     * @magentoConfigFixture current_store enterprise_reward/general/is_enabled  1
     */
    public function testIsRewardPointsEnabledInactiveEnabled()
    {
        $this->assertFalse($this->_importExportHelper->isRewardPointsEnabled());
    }

    /**
     * Is reward points enabled in config - inactive/disabled
     *
     * @magentoConfigFixture               modules/Enterprise_Reward/active      0
     * @magentoConfigFixture current_store enterprise_reward/general/is_enabled  0
     */
    public function testIsRewardPointsEnabledInactiveDisabled()
    {
        $this->assertFalse($this->_importExportHelper->isRewardPointsEnabled());
    }

    /**
     * Is customer balance enabled in config - active/enabled
     *
     * @magentoConfigFixture               modules/Enterprise_CustomerBalance/active       1
     * @magentoConfigFixture current_store customer/enterprise_customerbalance/is_enabled  1
     */
    public function testisCustomerBalanceEnabledActiveEnabled()
    {
        $this->assertTrue($this->_importExportHelper->isCustomerBalanceEnabled());
    }

    /**
     * Is customer balance enabled in config - active/disabled
     *
     * @magentoConfigFixture               modules/Enterprise_CustomerBalance/active       1
     * @magentoConfigFixture current_store customer/enterprise_customerbalance/is_enabled  0
     */
    public function testisCustomerBalanceEnabledActiveDisabled()
    {
        $this->assertFalse($this->_importExportHelper->isCustomerBalanceEnabled());
    }

    /**
     * Is customer balance enabled in config - inactive/enabled
     *
     * @magentoConfigFixture               modules/Enterprise_CustomerBalance/active       0
     * @magentoConfigFixture current_store customer/enterprise_customerbalance/is_enabled  1
     */
    public function testisCustomerBalanceEnabledInactiveEnabled()
    {
        $this->assertFalse($this->_importExportHelper->isCustomerBalanceEnabled());
    }

    /**
     * Is customer balance enabled in config - inactive/disabled
     *
     * @magentoConfigFixture               modules/Enterprise_CustomerBalance/active       0
     * @magentoConfigFixture current_store customer/enterprise_customerbalance/is_enabled  0
     */
    public function testisCustomerBalanceEnabledInactiveDisabled()
    {
        $this->assertFalse($this->_importExportHelper->isCustomerBalanceEnabled());
    }
}
