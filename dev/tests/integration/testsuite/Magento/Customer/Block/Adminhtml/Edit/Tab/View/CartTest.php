<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Controller\RegistryConstants;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class CartTest
 *
 * @magentoAppArea adminhtml
 */
class CartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Shopping cart.
     *
     * @var Cart
     */
    private $block;

    /**
     * Core registry.
     *
     * @var \Magento\Registry
     */
    private $coreRegistry;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get('Magento\App\State')->setAreaCode('adminhtml');

        $this->coreRegistry = $objectManager->get('Magento\Registry');
        $this->coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, 1);

        $this->block = $objectManager->get(
            'Magento\View\LayoutInterface'
        )->createBlock(
            'Magento\Customer\Block\Adminhtml\Edit\Tab\View\Cart',
            '',
            array('coreRegistry' => $this->coreRegistry, 'data' => array('website_id' => 1))
        );
        $this->block->getPreparedCollection();
    }

    /**
     * Execute per test cleanup.
     */
    public function tearDown()
    {
        $this->coreRegistry->unregister(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * Verify that the Url for a product row in the cart grid is correct.
     */
    public function testGetRowUrl()
    {
        $row = new \Magento\Object(array('product_id' => 1));
        $this->assertContains('catalog/product/edit/id/1', $this->block->getRowUrl($row));
    }

    /**
     * Verify that the headers in the cart grid are visible.
     */
    public function testGetHeadersVisibility()
    {
        $this->assertTrue($this->block->getHeadersVisibility());
    }

    /**
     * Verify that the customer has a single item in his cart.
     *
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/quote.php
     */
    public function testGetCollection()
    {
        $this->assertEquals(1, $this->block->getCollection()->getSize());
    }

    /**
     * Verify the basic content of an empty cart.
     *
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testToHtmlEmptyCart()
    {
        $this->assertEquals(0, $this->block->getCollection()->getSize());
        $this->assertContains("There are no items in customer's shopping cart at the moment", $this->block->toHtml());
    }

    /**
     * Verify the Html content for a single item in the customer's cart.
     *
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/quote.php
     */
    public function testToHtmlCartItem()
    {
        $html = $this->block->toHtml();
        $this->assertContains('Simple Product', $html);
        $this->assertContains('simple', $html);
        $this->assertContains('$10.00', $html);
        $this->assertContains('catalog/product/edit/id/1', $html);
    }
}
