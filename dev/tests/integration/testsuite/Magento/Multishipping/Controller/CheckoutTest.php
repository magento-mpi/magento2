<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Multishipping\Controller;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test class for \Magento\Multishipping\Controller\Checkout
 *
 * @magentoAppArea frontend
 */
class CheckoutTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * Covers app/code/Magento/Checkout/Block/Multishipping/Payment/Info.php
     * and app/code/Magento/Checkout/Block/Multishipping/Overview.php
     *
     * @magentoDataFixture Magento/Sales/_files/quote.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoConfigFixture current_store multishipping/options/checkout_multiple 1
     */
    public function testOverviewAction()
    {
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Quote');
        $quote->load('test01', 'reserved_order_id');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Checkout\Model\Session'
        )->setQuoteId(
            $quote->getId()
        );
        $formKey = $this->_objectManager->get('Magento\Framework\Data\Form\FormKey');
        $logger = $this->getMock('Magento\Framework\Logger', array(), array(), '', false);
        /** @var $session \Magento\Customer\Model\Session */
        $session = Bootstrap::getObjectManager()->create('Magento\Customer\Model\Session', array($logger));
        $service = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\CustomerAccountService'
        );
        $customer = $service->authenticate('customer@example.com', 'password');
        $session->setCustomerDataAsLoggedIn($customer);
        $this->getRequest()->setPost('payment', array('method' => 'checkmo'));
        $this->dispatch('multishipping/checkout/overview');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<div class="box box-method-billing">', $html);
        $this->assertContains('<div class="box box-order-shipping-method">', $html);
        $this->assertContains(
            '<dt class="title">' . $quote->getPayment()->getMethodInstance()->getTitle() . '</dt>',
            $html
        );
        $this->assertContains('<span class="price">$10.00</span>', $html);
        $this->assertContains('<input name="form_key" type="hidden" value="' . $formKey->getFormKey(), $html);
    }
}
