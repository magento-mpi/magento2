<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Checkout\Controller;

/**
 * @magentoDataFixture Magento/Sales/_files/quote.php
 */
class OnepageTest extends \Magento\TestFramework\TestCase\AbstractController
{
    protected function setUp()
    {
        parent::setUp();
        $quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Quote');
        $quote->load('test01', 'reserved_order_id');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Checkout\Model\Session'
        )->setQuoteId(
            $quote->getId()
        );
    }

    /**
     * Covers onepage payment.phtml templates
     */
    public function testIndexAction()
    {
        $this->dispatch('checkout/onepage/index');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<li id="opc-payment"', $html);
        $this->assertSelectCount('[id="checkout-payment-method-load"]', 1, $html);
        $this->assertSelectCount('form[id="co-billing-form"][action=""]', 1, $html);
        $this->assertSelectCount('form[id="co-payment-form"] input[name="form_key"]', 1, $html);
    }

    /**
     * Covers app/code/Magento/Checkout/Block/Onepage/Payment/Info.php
     */
    public function testProgressAction()
    {
        $steps = array(
            'payment' => array('is_show' => true, 'complete' => true),
            'billing' => array('is_show' => true),
            'shipping' => array('is_show' => true),
            'shipping_method' => array('is_show' => true)
        );
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Checkout\Model\Session'
        )->setSteps(
            $steps
        );

        $this->dispatch('checkout/onepage/progress');
        $html = $this->getResponse()->getBody();
        $this->assertContains('Checkout', $html);
        $methodTitle = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Checkout\Model\Session'
        )->getQuote()->getPayment()->getMethodInstance()->getTitle();
        $this->assertContains('<dt class="title">' . $methodTitle . '</dt>', $html);
    }

    public function testShippingMethodAction()
    {
        $this->dispatch('checkout/onepage/shippingmethod');
        $this->assertContains('no quotes are available', $this->getResponse()->getBody());
    }

    public function testReviewAction()
    {
        $this->dispatch('checkout/onepage/review');
        $this->assertContains('Place Order', $this->getResponse()->getBody());
        $this->assertContains('checkout-review', $this->getResponse()->getBody());
    }

    public function testSaveOrderActionWithoutFormKey()
    {
        $this->dispatch('checkout/onepage/saveOrder');
        $this->assertRedirect($this->stringContains('checkout/onepage'));
    }

    public function testSaveOrderActionWithFormKey()
    {
        $formKey = $this->_objectManager->get('\Magento\Framework\Data\Form\FormKey');
        $this->getRequest()->setParam('form_key', $formKey->getFormKey());
        $this->dispatch('checkout/onepage/saveOrder');
        $html = $this->getResponse()->getBody();
        $this->assertEquals(
            '{"success":false,"error":true,"error_messages":"Please specify a shipping method."}',
            $html,
            $html
        );
    }
}
