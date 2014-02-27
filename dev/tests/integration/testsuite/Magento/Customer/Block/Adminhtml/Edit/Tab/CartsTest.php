<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\Adminhtml\Index;

/**
 * Magento\Customer\Block\Adminhtml\Edit\Tab\Carts
 *
 * @magentoAppArea adminhtml
 */
class CartsTest extends \PHPUnit_Framework_TestCase
{
    /** @var Carts */
    private $_block;

    /** @var \Magento\Customer\Service\V1\CustomerServiceInterface */
    private $_customerService;

    /** @var \Magento\Backend\Block\Template\Context */
    private $_context;

    /** @var \Magento\ObjectManager */
    private $_objectManager;

    public function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_customerService = $this->_objectManager->get('Magento\Customer\Service\V1\CustomerServiceInterface');
        $storeManager = $this->_objectManager->get('Magento\Core\Model\StoreManager');
        $this->_context = $this->_objectManager
            ->get(
                'Magento\Backend\Block\Template\Context',
                array('storeManager' => $storeManager)
            );

    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetHtml()
    {
        $customer = $this->_customerService->getCustomer(1);
        $data = ['account' => $customer->__toArray()];
        $this->_context->getBackendSession()->setCustomerData($data);

        $this->_block = $this->_objectManager->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Customer\Block\Adminhtml\Edit\Tab\Carts', '', ['context' => $this->_context]);

        $html = $this->_block->toHtml();
        $this->assertContains("<div id=\"customer_cart_grid1\">", $html);
        $this->assertContains("<div class=\"grid-actions\">", $html);
        $this->assertContains("customer_cart_grid1JsObject = new varienGrid('customer_cart_grid1',", $html);
        $this->assertContains("backend/customer/cart_product_composite_cart/configure/website_id/1", $html);
    }

    public function testGetHtmlNoCustomer()
    {
        $data = ['account' => []];
        $this->_context->getBackendSession()->setCustomerData($data);

        $this->_block = $this->_objectManager->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Customer\Block\Adminhtml\Edit\Tab\Carts', '', ['context' => $this->_context]);

        $html = $this->_block->toHtml();
        $this->assertContains("<div id=\"customer_cart_grid0\">", $html);
        $this->assertContains("<div class=\"grid-actions\">", $html);
        $this->assertContains("customer_cart_grid0JsObject = new varienGrid('customer_cart_grid0',", $html);
        $this->assertContains("backend/customer/cart_product_composite_cart/configure/website_id/0/key/", $html);
    }


}
 