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
    /** @var  \Magento\CatalogInventory\Model\Stock\Status */
    protected $stockStatus;

    protected function setUp()
    {
        $this->stockStatus = $this->getMock(
            '\Magento\CatalogInventory\Model\Stock\Status',
            ['rebuild'],
            [],
            '',
            false
        );
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoConfigFixture default/cataloginventory/item_options/manage_stock 0
     */
    public function testSaveRebuildIndexPositive()
    {
        $this->stockStatus->expects($this->once())
            ->method('rebuild')
            ->will($this->returnValue($this->stockStatus));

        $manageStock = new Managestock(
            Bootstrap::getObjectManager()->get('\Magento\Framework\Model\Context'),
            Bootstrap::getObjectManager()->get('\Magento\Framework\Registry'),
            Bootstrap::getObjectManager()->get('\Magento\Framework\App\Config\ScopeConfigInterface'),
            $this->stockStatus,
            Bootstrap::getObjectManager()->get('Magento\Core\Model\Resource\Config')
        );

        $manageStock->setPath('cataloginventory/item_options/manage_stock');
        $manageStock->setScope('default');
        $manageStock->setScopeId(0);
        $manageStock->setValue(1);

        // assert
        $manageStock->save();

    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoConfigFixture default/cataloginventory/item_options/manage_stock 0
     */
    public function testSaveRebuildIndexNegative()
    {
        $this->stockStatus->expects($this->never())
            ->method('rebuild')
            ->will($this->returnValue($this->stockStatus));

        $manageStock = new Managestock(
            Bootstrap::getObjectManager()->get('\Magento\Framework\Model\Context'),
            Bootstrap::getObjectManager()->get('\Magento\Framework\Registry'),
            Bootstrap::getObjectManager()->get('\Magento\Framework\App\Config\ScopeConfigInterface'),
            $this->stockStatus,
            Bootstrap::getObjectManager()->get('Magento\Core\Model\Resource\Config')
        );

        $manageStock->setPath('cataloginventory/item_options/manage_stock');
        $manageStock->setScope('default');
        $manageStock->setScopeId(0);
        $manageStock->setValue(0);

        // assert
        $manageStock->save();

    }
}
