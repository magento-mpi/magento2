<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerFinance\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerFinance\Helper\Data
     */
    protected $_customerFinanceHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleManagerMock;

    /**
     * Set import/export helper
     *
     * @static
     */
    protected function setUp()
    {
        $this->_moduleManagerMock = $this->getMock('Magento\Framework\Module\Manager', [], [], '', false);
        $context = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\App\Helper\Context',
            ['moduleManager' => $this->_moduleManagerMock]
        );
        $this->_customerFinanceHelper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CustomerFinance\Helper\Data',
            ['context' => $context]
        );
    }

    /**
     * Is reward points enabled in config - active/enabled
     *
     * @magentoConfigFixture current_store magento_reward/general/is_enabled  1
     */
    public function testIsRewardPointsEnabledActiveEnabled()
    {
        $this->_moduleManagerMock->expects(
            $this->any()
        )->method(
            'isEnabled'
        )->with(
            'Magento_Reward'
        )->will(
            $this->returnValue(true)
        );
        $this->assertTrue($this->_customerFinanceHelper->isRewardPointsEnabled());
    }

    /**
     * Is reward points enabled in config - active/disabled
     *
     * @magentoConfigFixture current_store magento_reward/general/is_enabled  0
     */
    public function testIsRewardPointsEnabledActiveDisabled()
    {
        $this->_moduleManagerMock->expects(
            $this->any()
        )->method(
            'isEnabled'
        )->with(
            'Magento_Reward'
        )->will(
            $this->returnValue(true)
        );
        $this->assertFalse($this->_customerFinanceHelper->isRewardPointsEnabled());
    }

    /**
     * Is reward points enabled in config - inactive/enabled
     *
     * @magentoConfigFixture current_store magento_reward/general/is_enabled  1
     */
    public function testIsRewardPointsEnabledInactiveEnabled()
    {
        $this->_moduleManagerMock->expects(
            $this->any()
        )->method(
            'isEnabled'
        )->with(
            'Magento_Reward'
        )->will(
            $this->returnValue(null)
        );
        $this->assertFalse($this->_customerFinanceHelper->isRewardPointsEnabled());
    }

    /**
     * Is reward points enabled in config - inactive/disabled
     *
     * @magentoConfigFixture current_store magento_reward/general/is_enabled  0
     */
    public function testIsRewardPointsEnabledInactiveDisabled()
    {
        $this->_moduleManagerMock->expects(
            $this->any()
        )->method(
            'isEnabled'
        )->with(
            'Magento_Reward'
        )->will(
            $this->returnValue(null)
        );
        $this->assertFalse($this->_customerFinanceHelper->isRewardPointsEnabled());
    }

    /**
     * Is customer balance enabled in config - active/enabled
     *
     * @magentoConfigFixture current_store customer/magento_customerbalance/is_enabled  1
     */
    public function testisCustomerBalanceEnabledActiveEnabled()
    {
        $this->_moduleManagerMock->expects(
            $this->any()
        )->method(
            'isEnabled'
        )->with(
            'Magento_CustomerBalance'
        )->will(
            $this->returnValue(true)
        );
        $this->assertTrue($this->_customerFinanceHelper->isCustomerBalanceEnabled());
    }

    /**
     * Is customer balance enabled in config - active/disabled
     *
     * @magentoConfigFixture current_store customer/magento_customerbalance/is_enabled  0
     */
    public function testisCustomerBalanceEnabledActiveDisabled()
    {
        $this->_moduleManagerMock->expects(
            $this->any()
        )->method(
            'isEnabled'
        )->with(
            'Magento_CustomerBalance'
        )->will(
            $this->returnValue(true)
        );
        $this->assertFalse($this->_customerFinanceHelper->isCustomerBalanceEnabled());
    }

    /**
     * Is customer balance enabled in config - inactive/enabled
     *
     * @magentoConfigFixture current_store customer/magento_customerbalance/is_enabled  1
     */
    public function testisCustomerBalanceEnabledInactiveEnabled()
    {
        $this->_moduleManagerMock->expects(
            $this->any()
        )->method(
            'isEnabled'
        )->with(
            'Magento_CustomerBalance'
        )->will(
            $this->returnValue(null)
        );
        $this->assertFalse($this->_customerFinanceHelper->isCustomerBalanceEnabled());
    }

    /**
     * Is customer balance enabled in config - inactive/disabled
     *
     * @magentoConfigFixture current_store customer/magento_customerbalance/is_enabled  0
     */
    public function testisCustomerBalanceEnabledInactiveDisabled()
    {
        $this->_moduleManagerMock->expects(
            $this->any()
        )->method(
            'isEnabled'
        )->with(
            'Magento_CustomerBalance'
        )->will(
            $this->returnValue(null)
        );
        $this->assertFalse($this->_customerFinanceHelper->isCustomerBalanceEnabled());
    }
}
