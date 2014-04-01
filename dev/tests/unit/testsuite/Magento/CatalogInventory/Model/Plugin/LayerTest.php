<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Plugin;

class LayerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogInventory\Model\Plugin\Layer
     */
    protected $_model;

    /**
     * @var \Magento\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfigMock;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_stockStatusMock;

    public function setUp()
    {
        $this->_storeConfigMock = $this->getMock('\Magento\App\Config\ScopeConfigInterface');
        $this->_stockStatusMock = $this->getMock(
            '\Magento\CatalogInventory\Model\Stock\Status',
            array(),
            array(),
            '',
            false
        );

        $this->_model = new \Magento\CatalogInventory\Model\Plugin\Layer(
            $this->_stockStatusMock,
            $this->_storeConfigMock
        );
    }

    /**
     * Test add stock status to collection with disabled 'display out of stock' option
     */
    public function testAddStockStatusDisabledShow()
    {
        $this->_storeConfigMock->expects(
            $this->once()
        )->method(
            'isSetFlag'
        )->with(
            'cataloginventory/options/show_out_of_stock'
        )->will(
            $this->returnValue(true)
        );
        $collectionMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Collection',
            array(),
            array(),
            '',
            false
        );
        $this->_stockStatusMock->expects($this->never())->method('addIsInStockFilterToCollection');
        $subjectMock = $this->getMock('\Magento\Catalog\Model\Layer', array(), array(), '', false);
        $this->_model->beforePrepareProductCollection($subjectMock, $collectionMock);
    }

    /**
     *  Test add stock status to collection with 'display out of stock' option enabled
     */
    public function testAddStockStatusEnabledShow()
    {
        $this->_storeConfigMock->expects(
            $this->once()
        )->method(
            'isSetFlag'
        )->with(
            'cataloginventory/options/show_out_of_stock'
        )->will(
            $this->returnValue(false)
        );

        $collectionMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Collection',
            array(),
            array(),
            '',
            false
        );

        $this->_stockStatusMock->expects(
            $this->once()
        )->method(
            'addIsInStockFilterToCollection'
        )->with(
            $collectionMock
        );

        $subjectMock = $this->getMock('\Magento\Catalog\Model\Layer', array(), array(), '', false);
        $this->_model->beforePrepareProductCollection($subjectMock, $collectionMock);
    }
}
