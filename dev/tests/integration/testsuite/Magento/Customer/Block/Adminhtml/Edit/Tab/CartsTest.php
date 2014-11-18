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

    /** @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface */
    private $_customerAccountService;

    /** @var \Magento\Backend\Block\Template\Context */
    private $_context;

    /** @var \Magento\Framework\ObjectManager */
    private $_objectManager;

    public function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_customerAccountService = $this->_objectManager->get(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface'
        );
        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManager');
        $this->_context = $this->_objectManager->get(
            'Magento\Backend\Block\Template\Context',
            array('storeManager' => $storeManager)
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetHtml()
    {
        $customer = $this->_customerAccountService->getCustomer(1);
        $data = array('account' => $customer->__toArray());
        $this->_context->getBackendSession()->setCustomerData($data);

        $this->_block = $this->_objectManager->get(
            'Magento\Framework\View\LayoutInterface'
        )->createBlock(
            'Magento\Customer\Block\Adminhtml\Edit\Tab\Carts',
            '',
            array('context' => $this->_context)
        );

        $html = $this->_block->toHtml();
        $this->assertContains("<div id=\"customer_cart_grid1\">", $html);
        $this->assertContains("<div class=\"grid-actions\">", $html);
        $this->assertContains("customer_cart_grid1JsObject = new varienGrid('customer_cart_grid1',", $html);
        $this->assertContains("backend/customer/cart_product_composite_cart/configure/website_id/1", $html);
    }

    public function testGetHtmlNoCustomer()
    {
        $data = array('account' => array());
        $this->_context->getBackendSession()->setCustomerData($data);

        $this->_block = $this->_objectManager->get(
            'Magento\Framework\View\LayoutInterface'
        )->createBlock(
            'Magento\Customer\Block\Adminhtml\Edit\Tab\Carts',
            '',
            array('context' => $this->_context)
        );

        $html = $this->_block->toHtml();
        $this->assertContains("<div id=\"customer_cart_grid\">", $html);
        $this->assertContains("<div class=\"grid-actions\">", $html);
        $this->assertContains("customer_cart_gridJsObject = new varienGrid('customer_cart_grid',", $html);
        $this->assertContains("backend/customer/cart_product_composite_cart/configure/key/", $html);
    }
}
