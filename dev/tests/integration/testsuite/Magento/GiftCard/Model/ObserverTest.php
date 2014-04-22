<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture current_store giftcard/general/order_item_status 2
     * @magentoDataFixture Magento/GiftCard/_files/gift_card.php
     * @magentoDataFixture Magento/GiftCard/_files/order_with_gift_card.php
     */
    public function testGenerateGiftCardAccountsEmailSending()
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->loadArea(\Magento\Core\Model\App\Area::AREA_FRONTEND);
        $order = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Order');
        $this->_checkOrderItemProductOptions($order, true);

        $event = new \Magento\Event(array('order' => $order));
        $observer = new \Magento\Event\Observer(array('event' => $event));

        /** @var $model \Magento\GiftCard\Model\Observer */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\GiftCard\Model\Observer'
        );
        $model->generateGiftCardAccounts($observer);

        $this->_checkOrderItemProductOptions($order, false);
    }

    /**
     * Check email sending related gift card product options
     *
     * @param \Magento\Sales\Model\Order $order
     * @param bool $expectedEmpty
     */
    protected function _checkOrderItemProductOptions($order, $expectedEmpty)
    {
        $order->loadByIncrementId('100000001');
        $orderItems = $order->getAllItems();
        $orderItem = reset($orderItems);
        $options = $orderItem->getProductOptions();
        $this->assertEquals($expectedEmpty, empty($options['email_sent']));
        $this->assertEquals($expectedEmpty, empty($options['giftcard_created_codes']));
    }
}
