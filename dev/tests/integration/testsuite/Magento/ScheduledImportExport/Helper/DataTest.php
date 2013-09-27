<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ScheduledImportExport_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ScheduledImportExport_Helper_Data
     */
    protected $_importExportHelper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleManagerMock;

    /**
     * Set import/export helper
     *
     * @static
     */
    protected function setUp()
    {
        $this->_moduleManagerMock = $this->getMock('Magento_Core_Model_ModuleManager', array(), array(), '', false);
        $context = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Helper_Context', array('moduleManager' => $this->_moduleManagerMock));
        $this->_importExportHelper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create(
            'Magento_ScheduledImportExport_Helper_Data', array('context' => $context)
        );
    }

    /**
     * Is reward points enabled in config - active/enabled
     *
     * @magentoConfigFixture current_store magento_reward/general/is_enabled  1
     */
    public function testIsRewardPointsEnabledActiveEnabled()
    {
        $this->_moduleManagerMock->expects($this->any())->method('isEnabled')->with('Magento_Reward')
            ->will($this->returnValue(true));
        $this->assertTrue($this->_importExportHelper->isRewardPointsEnabled());
    }

    /**
     * Is reward points enabled in config - active/disabled
     *
     * @magentoConfigFixture current_store magento_reward/general/is_enabled  0
     */
    public function testIsRewardPointsEnabledActiveDisabled()
    {
        $this->_moduleManagerMock->expects($this->any())->method('isEnabled')->with('Magento_Reward')
            ->will($this->returnValue(true));
        $this->assertFalse($this->_importExportHelper->isRewardPointsEnabled());
    }

    /**
     * Is reward points enabled in config - inactive/enabled
     *
     * @magentoConfigFixture current_store magento_reward/general/is_enabled  1
     */
    public function testIsRewardPointsEnabledInactiveEnabled()
    {
        $this->_moduleManagerMock->expects($this->any())->method('isEnabled')->with('Magento_Reward')
            ->will($this->returnValue(null));
        $this->assertFalse($this->_importExportHelper->isRewardPointsEnabled());
    }

    /**
     * Is reward points enabled in config - inactive/disabled
     *
     * @magentoConfigFixture current_store magento_reward/general/is_enabled  0
     */
    public function testIsRewardPointsEnabledInactiveDisabled()
    {
        $this->_moduleManagerMock->expects($this->any())->method('isEnabled')->with('Magento_Reward')
            ->will($this->returnValue(null));
        $this->assertFalse($this->_importExportHelper->isRewardPointsEnabled());
    }

    /**
     * Is customer balance enabled in config - active/enabled
     *
     * @magentoConfigFixture current_store customer/magento_customerbalance/is_enabled  1
     */
    public function testisCustomerBalanceEnabledActiveEnabled()
    {
        $this->_moduleManagerMock->expects($this->any())->method('isEnabled')->with('Magento_CustomerBalance')
            ->will($this->returnValue(true));
        $this->assertTrue($this->_importExportHelper->isCustomerBalanceEnabled());
    }

    /**
     * Is customer balance enabled in config - active/disabled
     *
     * @magentoConfigFixture current_store customer/magento_customerbalance/is_enabled  0
     */
    public function testisCustomerBalanceEnabledActiveDisabled()
    {
        $this->_moduleManagerMock->expects($this->any())->method('isEnabled')->with('Magento_CustomerBalance')
            ->will($this->returnValue(true));
        $this->assertFalse($this->_importExportHelper->isCustomerBalanceEnabled());
    }

    /**
     * Is customer balance enabled in config - inactive/enabled
     *
     * @magentoConfigFixture current_store customer/magento_customerbalance/is_enabled  1
     */
    public function testisCustomerBalanceEnabledInactiveEnabled()
    {
        $this->_moduleManagerMock->expects($this->any())->method('isEnabled')->with('Magento_CustomerBalance')
            ->will($this->returnValue(null));
        $this->assertFalse($this->_importExportHelper->isCustomerBalanceEnabled());
    }

    /**
     * Is customer balance enabled in config - inactive/disabled
     *
     * @magentoConfigFixture current_store customer/magento_customerbalance/is_enabled  0
     */
    public function testisCustomerBalanceEnabledInactiveDisabled()
    {
        $this->_moduleManagerMock->expects($this->any())->method('isEnabled')->with('Magento_CustomerBalance')
            ->will($this->returnValue(null));
        $this->assertFalse($this->_importExportHelper->isCustomerBalanceEnabled());
    }
}
