<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\View;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Customer\Controller\Adminhtml\Index;

/**
 * Class OrdersTest
 *
 * @magentoAppArea adminhtml
 */
class OrdersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The orders block under test.
     *
     * @var Orders
     */
    private $block;

    /**
     * Core registry.
     *
     * @var \Magento\Core\Model\Registry
     */
    private $coreRegistry;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get('Magento\App\State')->setAreaCode('adminhtml');

        $this->coreRegistry = $objectManager->get('Magento\Core\Model\Registry');
        $this->coreRegistry->register(Index::REGISTRY_CURRENT_CUSTOMER_ID, 1);

        $this->block = $objectManager->get('Magento\View\LayoutInterface')
            ->createBlock(
                'Magento\Customer\Block\Adminhtml\Edit\Tab\View\Orders',
                '',
                ['coreRegistry' => $this->coreRegistry]
            );
        $this->block->getPreparedCollection();
    }

    /**
     * Execute post test cleanup.
     */
    public function tearDown()
    {
        $this->coreRegistry->unregister(Index::REGISTRY_CURRENT_CUSTOMER_ID);
        $this->block->setCollection(null);
    }

    /**
     * Verify that the correct Url is return for a row in the orders grid.
     */
    public function testGetRowUrl()
    {
        $row = new \Magento\Object();
        $row->setId(1);
        $this->assertContains('sales/order/view/order_id/1', $this->block->getRowUrl($row));
    }

    /**
     * Verify that the grid headers are visible.
     */
    public function testGetHeadersVisibility()
    {
        $this->assertTrue($this->block->getHeadersVisibility());
    }

    /**
     * Verify the integrity of the orders collection.
     */
    public function testGetCollection()
    {
        $collection = $this->block->getCollection();
        $this->assertEquals(0, $collection->getSize());
        $this->assertEquals(5, $collection->getPageSize());
        $this->assertEquals(1, $collection->getCurPage());
    }

    /**
     * Check the grid Html.
     */
    public function testToHtml()
    {
        $this->assertContains("We couldn't find any records.", $this->block->toHtml());
    }
}
