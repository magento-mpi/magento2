<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Centinel
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Centinel;

/**
 * @magentoAppArea adminhtml
 */
class CreateOrderTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @magentoConfigFixture default_store payment/ccsave/centinel 1
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testIndexAction()
    {
        /** @var $order \Magento\Sales\Model\AdminOrder\Create */
        $order = $this->_objectManager->get('Magento\Sales\Model\AdminOrder\Create');
        $paymentData = array(
            'cc_owner' => 'Test User',
            'cc_type' => 'visa',
            'cc_number' => '4111111111111111',
            'cc_exp_month' => '12',
            'cc_exp_year' => '2013',
            'cc_cid' => '123',
            'method' => 'ccsave',
        );
        $quote = $order->addProducts(array(1 => array('qty' => 1)))->getQuote();
        $defaultStoreId = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
            ->getStore('default')->getId();
        $quote->setStoreId($defaultStoreId);
        $quote->getPayment()->addData($paymentData);
        $this->dispatch('backend/sales/order_create/index');
        $this->assertContains('<div class="centinel">', $this->getResponse()->getBody());
    }
}
