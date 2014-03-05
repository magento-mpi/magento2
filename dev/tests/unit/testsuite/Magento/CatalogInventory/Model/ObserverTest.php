<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\CatalogInventory\Model\Observer
     */
    protected $_model;

    /**
     * @var \Magento\Core\Model\Store\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfigMock;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_stockStatusMock;

    public function setUp()
    {
        $proceIndexerMock = $this->getMock(
            '\Magento\Catalog\Model\Indexer\Product\Price\Processor', array(), array(), '', false
        );
        $stockIndexerMock = $this->getMock(
            '\Magento\CatalogInventory\Model\Resource\Indexer\Stock', array(), array(), '', false
        );
        $resourceStockMock = $this->getMock(
            '\Magento\CatalogInventory\Model\Resource\Stock', array(), array(), '', false
        );
        $indexerMock = $this->getMock('\Magento\Index\Model\Indexer', array(), array(), '', false);
        $stockMock = $this->getMock('\Magento\CatalogInventory\Model\Stock', array(), array(), '', false);

        $catalogInventoryDataMock = $this->getMock(
            '\Magento\CatalogInventory\Helper\Data', array(), array(), '', false
        );
        $stockItemFactoryMock = $this->getMock(
            '\Magento\CatalogInventory\Model\Stock\ItemFactory', array(), array(), '', false
        );
        $stockFactoryMock = $this->getMock(
            '\Magento\CatalogInventory\Model\StockFactory', array(), array(), '', false
        );
        $stockStatusFactoryMock = $this->getMock(
            '\Magento\CatalogInventory\Model\Stock\StatusFactory', array(), array(), '', false
        );
        $this->_storeConfigMock = $this->getMock(
            '\Magento\Core\Model\Store\Config', array('getConfigFlag'), array(), '', false
        );
        $this->_storeManagerMock = $this->getMock(
            '\Magento\Core\Model\StoreManager', array('getWebsite'), array(), '', false
        );
        $this->_stockStatusMock = $this->getMock(
            '\Magento\CatalogInventory\Model\Stock\Status', array(), array(), '', false
        );

        $this->_model = new \Magento\CatalogInventory\Model\Observer(
            $proceIndexerMock,
            $stockIndexerMock,
            $resourceStockMock,
            $indexerMock,
            $stockMock,
            $this->_stockStatusMock,
            $catalogInventoryDataMock,
            $stockItemFactoryMock,
            $stockFactoryMock,
            $stockStatusFactoryMock,
            $this->_storeConfigMock,
            $this->_storeManagerMock
        );
    }

    public function testAddStockStatusDisabledShow()
    {
        $this->_storeConfigMock->expects($this->once())
            ->method('getConfigFlag')
            ->with('cataloginventory/options/show_out_of_stock')
            ->will($this->returnValue(true));
        $observerMock = $this->getMock('\Magento\Event\Observer', array('getEvent'), array(), '', false);
        $observerMock->expects($this->never())->method('getEvent');
        $this->_model->addStockStatusLimitation($observerMock);
    }

    public function testAddStockStatusEnabledShow()
    {
        $this->_storeConfigMock->expects($this->once())
            ->method('getConfigFlag')
            ->with('cataloginventory/options/show_out_of_stock')
            ->will($this->returnValue(false));
        $observerMock = $this->getMock('\Magento\Event\Observer', array('getEvent'), array(), '', false);
        $eventMock = $this->getMock('\Magento\Event', array('getCollection'), array(), '', false);
        $collectionMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Collection',
            array('hasFlag', 'getSelect', 'setFlag', 'getEntity'),
            array(),
            '',
            false
        );
        $selectMock = $this->getMock('\Magento\DB\Select', array(), array(), '', false);
        $eventMock->expects($this->any())->method('getCollection')->will($this->returnValue($collectionMock));
        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $collectionMock->expects($this->any())->method('getSelect')->will($this->returnValue($selectMock));
        $websiteMock = $this->getMock('\Magento\Core\Model\Website', array(), array(), '', false);

        $this->_storeManagerMock->expects($this->any())->method('getWebsite')->will($this->returnValue($websiteMock));

        $entityMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product', array('getEntityIdField'), array(), '', false
        );

        $collectionMock->expects($this->any())->method('getEntity')->will($this->returnValue($entityMock));

        $entityMock->expects($this->once())
            ->method('getEntityIdField')
            ->will($this->returnValue('entity_id_field_name'));

        $websiteMock->expects($this->once())
            ->method('getIdFieldName')
            ->will($this->returnValue('website_id_field_name'));

        $this->_stockStatusMock->expects($this->once())->method('prepareCatalogProductIndexSelect')
            ->with($selectMock, 'entity_id_field_name', 'website_id_field_name');

        $collectionMock->expects($this->once())->method('hasFlag')->will($this->returnValue(false));
        $collectionMock->expects($this->once())->method('setFlag')->with('applied_stock_status_limitation', true);

        $this->_model->addStockStatusLimitation($observerMock);
    }
}
