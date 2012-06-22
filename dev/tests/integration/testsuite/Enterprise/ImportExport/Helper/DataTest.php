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

class Mage_ImportExport_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_ImportExport_Helper_Data
     */
    protected static $_importExportHelper;

    /**
     * Set import/export helper
     *
     * @static
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$_importExportHelper = Mage::helper('Enterprise_ImportExport_Helper_Data');
    }

    /**
     * Unset helper
     *
     * @static
     */
    public static function tearDownAfterClass()
    {
        self::$_importExportHelper = null;
    }

    /**
     * Is reward points enabled in config - active/enabled
     *
     * @magentoConfigFixture               modules/Enterprise_Reward/active      1
     * @magentoConfigFixture current_store enterprise_reward/general/is_enabled  1
     */
    public function testIsRewardPointsEnabledActiveEnabled()
    {
        $this->assertTrue(self::$_importExportHelper->isRewardPointsEnabled());
    }

    /**
     * Is reward points enabled in config - active/disabled
     *
     * @magentoConfigFixture               modules/Enterprise_Reward/active      1
     * @magentoConfigFixture current_store enterprise_reward/general/is_enabled  0
     */
    public function testIsRewardPointsEnabledActiveDisabled()
    {
        $this->assertFalse(self::$_importExportHelper->isRewardPointsEnabled());
    }

    /**
     * Is reward points enabled in config - inactive/enabled
     *
     * @magentoConfigFixture               modules/Enterprise_Reward/active      0
     * @magentoConfigFixture current_store enterprise_reward/general/is_enabled  1
     */
    public function testIsRewardPointsEnabledInactiveEnabled()
    {
        $this->assertFalse(self::$_importExportHelper->isRewardPointsEnabled());
    }

    /**
     * Is reward points enabled in config - inactive/disabled
     *
     * @magentoConfigFixture               modules/Enterprise_Reward/active      0
     * @magentoConfigFixture current_store enterprise_reward/general/is_enabled  0
     */
    public function testIsRewardPointsEnabledInactiveDisabled()
    {
        $this->assertFalse(self::$_importExportHelper->isRewardPointsEnabled());
    }

    /**
     * Is customer balance enabled in config - active/enabled
     *
     * @magentoConfigFixture               modules/Enterprise_CustomerBalance/active       1
     * @magentoConfigFixture current_store customer/enterprise_customerbalance/is_enabled  1
     */
    public function testisCustomerBalanceEnabledActiveEnabled()
    {
        $this->assertTrue(self::$_importExportHelper->isCustomerBalanceEnabled());
    }

    /**
     * Is customer balance enabled in config - active/disabled
     *
     * @magentoConfigFixture               modules/Enterprise_CustomerBalance/active       1
     * @magentoConfigFixture current_store customer/enterprise_customerbalance/is_enabled  0
     */
    public function testisCustomerBalanceEnabledActiveDisabled()
    {
        $this->assertFalse(self::$_importExportHelper->isCustomerBalanceEnabled());
    }

    /**
     * Is customer balance enabled in config - inactive/enabled
     *
     * @magentoConfigFixture               modules/Enterprise_CustomerBalance/active       0
     * @magentoConfigFixture current_store customer/enterprise_customerbalance/is_enabled  1
     */
    public function testisCustomerBalanceEnabledInactiveEnabled()
    {
        $this->assertFalse(self::$_importExportHelper->isCustomerBalanceEnabled());
    }

    /**
     * Is customer balance enabled in config - inactive/disabled
     *
     * @magentoConfigFixture               modules/Enterprise_CustomerBalance/active       0
     * @magentoConfigFixture current_store customer/enterprise_customerbalance/is_enabled  0
     */
    public function testisCustomerBalanceEnabledInactiveDisabled()
    {
        $this->assertFalse(self::$_importExportHelper->isCustomerBalanceEnabled());
    }
}
