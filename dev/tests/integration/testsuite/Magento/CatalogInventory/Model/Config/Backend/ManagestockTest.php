<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Config\Backend;

use Magento\TestFramework\Helper\Bootstrap as Bootstrap;

/**
 * Class ManagestockTest
 */
class ManagestockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testSaveAndRebuildIndex
     * @return array
     */
    public function saveAndRebuildIndexDataProvider()
    {
        return [
            [1, 1],
            [0, 0],
        ];
    }

    /**
     * Test rebuild stock indexer on stock status config save
     *
     * @dataProvider saveAndRebuildIndexDataProvider
     *
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoConfigFixture default/cataloginventory/item_options/manage_stock 0
     *
     * @param int $newStockValue new value for stock status
     * @param int $callCount count matcher
     */
    public function testSaveAndRebuildIndex($newStockValue, $callCount)
    {
        /** @var \Magento\CatalogInventory\Model\Stock\Status */
        $stockStatus = $this->getMock(
            '\Magento\CatalogInventory\Model\Stock\Status',
            ['rebuild'],
            [],
            '',
            false
        );

        $stockStatus->expects($this->exactly($callCount))
            ->method('rebuild')
            ->will($this->returnValue($stockStatus));

        $manageStock = new Managestock(
            Bootstrap::getObjectManager()->get('\Magento\Framework\Model\Context'),
            Bootstrap::getObjectManager()->get('\Magento\Framework\Registry'),
            Bootstrap::getObjectManager()->get('\Magento\Framework\App\Config\ScopeConfigInterface'),
            $stockStatus,
            Bootstrap::getObjectManager()->get('Magento\Core\Model\Resource\Config')
        );

        $manageStock->setPath('cataloginventory/item_options/manage_stock')
            ->setScope('default')
            ->setScopeId(0);

        $manageStock->setValue($newStockValue);

        // assert
        $manageStock->save();
    }
}
