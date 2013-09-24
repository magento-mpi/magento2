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
     * @magentoConfigFixture admin_store payment/ccsave/centinel 1
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testIndexAction()
    {
        /** @var $order \Magento\Adminhtml\Model\Sales\Order\Create */
        $order = $this->_objectManager->get('Magento\Adminhtml\Model\Sales\Order\Create');
        $paymentData = array(
            'cc_owner' => 'Test User',
            'cc_type' => 'visa',
            'cc_number' => '4111111111111111',
            'cc_exp_month' => '12',
            'cc_exp_year' => '2013',
            'cc_cid' => '123',
            'method' => 'ccsave',
        );
        $order->addProducts(array(1 => array('qty' => 1)))->getQuote()->getPayment()->addData($paymentData);
        $this->dispatch('backend/admin/sales_order_create/index');
        $this->assertContains('<div class="centinel">', $this->getResponse()->getBody());
    }
}
