<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Adminhtml\Stock;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogInventory\Model\Adminhtml\Stock\Item|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * setUp
     */
    protected function setUp()
    {
        $resourceMock = $this->getMock(
            'Magento\Core\Model\Resource\AbstractResource',
            array('_construct', '_getReadAdapter', '_getWriteAdapter', 'getIdFieldName'),
            array(), '', false
        );

        $this->_model = new \Magento\CatalogInventory\Model\Adminhtml\Stock\Item(
            $this->getMock('Magento\Model\Context', array(), array(), '', false),
            $this->getMock('Magento\Registry', array(), array(), '', false),
            $this->getMock('Magento\Customer\Model\Session', array(), array(), '', false),
            $this->getMock('Magento\Index\Model\Indexer', array(), array(), '', false),
            $this->getMock('Magento\CatalogInventory\Model\Stock\Status', array(), array(), '', false),
            $this->getMock('Magento\CatalogInventory\Helper\Data', array(), array(), '', false),
            $this->getMock('Magento\CatalogInventory\Helper\Minsaleqty', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\StoreManagerInterface', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\LocaleInterface', array(), array(), '', false),
            $this->getMock('Magento\Math\Division', array(), array(), '', false),
            $resourceMock,
            $this->getMock('Magento\Data\Collection\Db', array(), array(), '', false),
            array()
        );
    }

    public function testGetCustomerGroupId()
    {
        $this->_model->setCustomerGroupId(null);
        $this->assertEquals(32000, $this->_model->getCustomerGroupId());
        $this->_model->setCustomerGroupId(2);
        $this->assertEquals(2, $this->_model->getCustomerGroupId());
    }

    public function testIsQtyCheckApplicable()
    {
        $this->assertTrue($this->_model->checkQty(1.0));
    }

    public function testCheckQuoteItemQty()
    {
        $this->_model->setData('manage_stock', 1);
        $this->_model->setData('is_in_stock', 1);
        $this->_model->setProductName('qwerty');
        $this->_model->setData('backorders', 3);
        $result = $this->_model->checkQuoteItemQty(1, 1);
        $this->assertEquals('We don\'t have as many "qwerty" as you requested.', $result->getMessage());
    }
}
