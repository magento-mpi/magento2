<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Rss\Product;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class NotifyStockTest
 * @package Magento\Catalog\Model\Rss\Product
 */
class NotifyStockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Rss\Product\NotifyStock
     */
    protected $notifyStock;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $status;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $managerInterface;

    protected function setUp()
    {
        $this->productFactory = $this->getMock('Magento\Catalog\Model\ProductFactory');
        $this->stockFactory = $this->getMock('Magento\CatalogInventory\Model\Resource\StockFactory');
        $this->status = $this->getMock('Magento\Catalog\Model\Product\Attribute\Source\Status');
        $this->managerInterface = $this->getMock('Magento\Framework\Event\ManagerInterface');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->notifyStock = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Model\Rss\Product\NotifyStock',
            [
                'productFactory' => $this->productFactory,
                'stockFactory' => $this->stockFactory,
                'productStatus' => $this->status,
                'eventManager' => $this->managerInterface
            ]
        );
    }

    public function testGetProductsCollection()
    {
    }
}
