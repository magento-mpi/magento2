<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Model\Backend;

/**
 * Test class for \Magento\GiftCardAccount\Model\Backend\History.
 * @magentoAppArea adminhtml
 * @magentoDataFixture Magento/GiftCardAccount/_files/giftcardaccount.php
 */
class HistoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var History
     */
    protected $_model;

    /**
     * @var \Magento\GiftCardAccount\Model\Giftcardaccount
     */
    protected $_giftcardAccount;

    protected function setUp()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Backend\Model\Auth\Session')->setUser(new \Magento\Object(array(
                'id' => 1,
                'username' => 'Admin user'
            )));
        $this->_giftcardAccount = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\GiftCardAccount\Model\Giftcardaccount');
        $this->_giftcardAccount->loadByCode('giftcardaccount_fixture');
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\GiftCardAccount\Model\History');
        $this->_model->setGiftcardaccount($this->_giftcardAccount);
    }

    /**
     * @covers \Magento\GiftCardAccount\Model\Backend\History::_getCreatedAdditionalInfo
     */
    public function testCreatedAdditionalInfo()
    {
        $this->_giftcardAccount->setHistoryAction(\Magento\GiftCardAccount\Model\History::ACTION_CREATED);
        $this->_model->save();
        $this->assertEquals(__('By admin: %1.', 'Admin user'), $this->_model->getAdditionalInfo());
    }

    /**
     * @covers \Magento\GiftCardAccount\Model\Backend\History::_getUpdatedAdditionalInfo
     */
    public function testUpdatedAdditionalInfo()
    {
        $this->_giftcardAccount->setHistoryAction(\Magento\GiftCardAccount\Model\History::ACTION_UPDATED);
        $this->_model->save();
        $this->assertEquals(__('By admin: %1.', 'Admin user'), $this->_model->getAdditionalInfo());
    }

    /**
     * @param string $recipientName
     * @dataProvider recipientDataProvider
     * @covers \Magento\GiftCardAccount\Model\Backend\History::_getSentAdditionalInfo
     */
    public function testSentAdditionalInfo($recipientName)
    {
        $recipientEmail = 'email@example.com';
        $this->_giftcardAccount->setRecipientEmail($recipientEmail);
        $this->_giftcardAccount->setRecipientName($recipientName);
        $this->_giftcardAccount->setHistoryAction(\Magento\GiftCardAccount\Model\History::ACTION_SENT);
        $this->_model->save();
        if ($recipientName) {
            $this->assertEquals(
                __('Recipient: %1.', "{$recipientName} <{$recipientEmail}>") . ' ' . __('By admin: %1.', 'Admin user'),
                $this->_model->getAdditionalInfo()
            );
        } else {
            $this->assertEquals(
                __('Recipient: %1.', $recipientEmail) . ' ' . __('By admin: %1.', 'Admin user'),
                $this->_model->getAdditionalInfo()
            );
        }
    }

    public function recipientDataProvider()
    {
        return array(
            array(null),
            array('recipient'),
        );
    }
}
