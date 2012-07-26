<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftCard
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCard_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture current_store giftcard/general/order_item_status 2
     * @magentoDataFixture Enterprise/GiftCard/_files/gift_card.php
     * @magentoDataFixture Enterprise/GiftCard/_files/order_with_gift_card.php
     */
    public function testGenerateGiftCardAccountsEmailSending()
    {
        $order = new Mage_Sales_Model_Order();
        $this->_checkOrderItemProductOptions($order, true);

        $event = new Varien_Event(array('order' => $order));
        $observer = new Varien_Event_Observer(array('event' => $event));

        $emailTemplateModel = new Mage_Core_Model_Email_Template();
        $model = new Enterprise_GiftCard_Model_Observer(array(
            'email_template_model' => $emailTemplateModel,
        ));
        $model->generateGiftCardAccounts($observer);

        $this->assertEquals(
            array('area' => Mage_Core_Model_App_Area::AREA_FRONTEND, 'store' => 1),
            $emailTemplateModel->getDesignConfig()->getData()
        );

        $this->_checkOrderItemProductOptions($order, false);
    }

    /**
     * Check email sending related gift card product options
     *
     * @param Mage_Sales_Model_Order $order
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
