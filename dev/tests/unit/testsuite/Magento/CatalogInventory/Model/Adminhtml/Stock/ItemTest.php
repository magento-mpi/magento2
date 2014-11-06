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
            'Magento\Framework\Model\Resource\AbstractResource',
            array('_construct', '_getReadAdapter', '_getWriteAdapter', 'getIdFieldName'),
            array(),
            '',
            false
        );
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $groupManagement = $this->getMockBuilder('Magento\Customer\Api\GroupManagementInterface')
            ->setMethods(['getAllGroup'])
            ->getMockForAbstractClass();

        $allGroup = $this->getMockBuilder('Magento\Customer\Api\Data\GroupInterface')
            ->setMethods(['getId'])
            ->getMockForAbstractClass();

        $allGroup->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(32000));

        $groupManagement->expects($this->any())
            ->method('getAllGroup')
            ->will($this->returnValue($allGroup));

        $this->_model = $objectHelper->getObject(
            '\Magento\CatalogInventory\Model\Adminhtml\Stock\Item',
            array(
                'resource' => $resourceMock,
                'groupManagement' => $groupManagement
            )
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
        $this->_model->setData('backorders', \Magento\CatalogInventory\Model\Stock::BACKORDERS_YES_NONOTIFY);
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

    public function testHasAdminArea()
    {
        $this->assertTrue($this->_model->hasAdminArea());
    }
}
