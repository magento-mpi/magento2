<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cardgate
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Magento/Sales/_files/order.php
 */
class Magento_Cardgate_Controller_CardgateTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function setUp()
    {
        parent::setUp();

        $order = Mage::getModel('Magento_Sales_Model_Order');
        $order->load('100000001', 'increment_id');
        $order->getPayment()->setMethod('cardgate_creditcard');

        $quote = Mage::getModel('Magento_Sales_Model_Quote')
            ->setStoreId($order->getStoreId())
            ->save();

        $order->setQuoteId($quote->getId());
        $order->setBaseGrandTotal(12.34);
        $order->setBaseTotalPaid(0);
        $order->save();

        $session = Mage::getSingleton('Magento_Checkout_Model_Session');
        $session->setLastRealOrderId($order->getRealOrderId())
            ->setLastQuoteId($order->getQuoteId());
    }

    public function testRedirectActionIsContentGenerated()
    {
        $this->dispatch('cardgate/cardgate/redirect/model/creditcard');
        $this->assertContains(
            '<form action="https://gateway.cardgateplus.com/" id="cardgateplus_checkout"'
            . ' name="cardgateplus_checkout" method="POST">',
            $this->getResponse()->getBody()
        );
    }

    public function testCancelActionIsRedirected()
    {
        $this->dispatch('cardgate/cardgate/cancel');
        $this->assertRedirect($this->stringEndsWith("checkout/cart/"));
    }

    public function testSuccessActionIsRedirected()
    {
        $this->dispatch('cardgate/cardgate/success');
        $this->assertRedirect($this->stringEndsWith("checkout/onepage/success/"));
    }

    public function testControlActionNoPost()
    {
        $this->dispatch('cardgate/cardgate/control');
        $this->assertEmpty($this->getResponse()->getBody());
    }

    /**
     * Set POST data and hash for control action
     *
     * @param string $amount
     * @return array
     */
    protected function _setControlActionData($amount)
    {
        $data = array();
        $data['transaction_id'] = 1;
        $data['currency'] = 'USD';
        $data['amount'] = $amount;
        $data['ref'] = '100000001';
        $data['status'] = Magento_Sales_Model_Order::STATE_PROCESSING;
        $data['hash'] =  md5('TEST' . $data['transaction_id'] . $data['currency'] . $data['amount'] . $data['ref']
        . $data['status'] . '263748');

        $this->getRequest()->setServer(array('REQUEST_METHOD' => 'POST'));
        $this->getRequest()->setPost($data);

        return $data;
    }

    /**
     * @magentoConfigFixture current_store payment/cardgate/test_mode 1
     * @magentoConfigFixture current_store payment/cardgate/hash_key 263748
     */
    public function testControlActionWrongAmount()
    {
        $this->_setControlActionData('1212');

        $this->dispatch('cardgate/cardgate/control');
        $this->assertEmpty($this->getResponse()->getBody());
    }

    /**
     * @magentoConfigFixture current_store payment/cardgate/test_mode 1
     * @magentoConfigFixture current_store payment/cardgate/hash_key 263748
     */
    public function testControlAction()
    {
        $data = $this->_setControlActionData('1234');

        $this->dispatch('cardgate/cardgate/control');
        $this->assertEquals($data['transaction_id'] . '.' . $data['status'], $this->getResponse()->getBody());
    }
}
