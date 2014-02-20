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
 * Magento\Customer\Block\Adminhtml\Edit\Tab\Cart
 *
 * @magentoAppArea adminhtml
 */
class CartsTest extends \PHPUnit_Framework_TestCase
{
    /** @var Carts */
    private $_block;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $customerService = $objectManager->get('Magento\Customer\Service\V1\CustomerServiceInterface');
        $storeManager = $objectManager->get('Magento\Core\Model\StoreManager');
        $context = $objectManager
            ->get(
                'Magento\Backend\Block\Template\Context',
                array('storeManager' => $storeManager)
            );

        $customer = $customerService->getCustomer(1);
        $data = ['account' => $customer->__toArray()];
        $context->getBackendSession()->setCustomerData($data);

        $this->_block = $objectManager->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Customer\Block\Adminhtml\Edit\Tab\Carts', '', ['context' => $context]);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetHtml()
    {
        $html = $this->_block->toHtml();
        $this->assertContains("<div id=\"customer_cart_grid1\">", $html);
        $this->assertContains("<div class=\"grid-actions\">", $html);
        $this->assertContains("customer_cart_grid1JsObject = new varienGrid('customer_cart_grid1',", $html);
    }
}
 