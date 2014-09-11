<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Model\Adminhtml\Balance;

/**
 * Test class for \Magento\CustomerBalance\Model\Adminhtml\Balance\History.
 * @magentoAppArea adminhtml
 * @magentoDataFixture Magento/CustomerBalance/_files/history.php
 */
class HistoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerBalance\Model\Balance
     */
    protected $_balance;

    /**
     * @var \Magento\CustomerBalance\Model\Balance\History
     */
    protected $_model;

    protected function setUp()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Backend\Model\Auth\Session'
        )->setUser(
            new \Magento\Framework\Object(array('id' => 1, 'username' => 'Admin user'))
        );
        $websiteId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\StoreManagerInterface'
        )->getStore()->getWebsiteId();
        $customer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Model\Customer'
        )->setWebsiteId(
            $websiteId
        )->loadByEmail(
            'customer@example.com'
        );
        $this->_balance = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CustomerBalance\Model\Balance'
        )->setCustomer(
            $customer
        )->loadByCustomer();
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CustomerBalance\Model\Balance\History'
        )->setCustomerId(
            $customer->getId()
        )->setWebsiteId(
            $websiteId
        )->setBalanceModel(
            $this->_balance
        );
    }

    /**
     * @param null|string $comment
     * @dataProvider additionalInfoDataProvider
     * @covers \Magento\CustomerBalance\Model\Adminhtml\Balance\History::_beforeSave
     */
    public function testAdditionalInfo($comment)
    {
        $this->_balance->setHistoryAction(
            \Magento\CustomerBalance\Model\Balance\History::ACTION_UPDATED
        )->unsUpdatedActionAdditionalInfo()->setComment(
            $comment
        );
        $this->_model->save();
        $expected = isset(
            $comment
        ) ? __(
            'By admin: %1. (%2)',
            'Admin user',
            $comment
        ) : __(
            'By admin: %1.',
            'Admin user'
        );
        $this->assertEquals($expected, $this->_model->getAdditionalInfo());
    }

    public function additionalInfoDataProvider()
    {
        return array(array('some comment'), array(null));
    }
}
