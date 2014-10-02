<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer\Search;

class StateKeyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $queryFactoryMock;

    /**
     * @var \Magento\Catalog\Model\Layer\Search\StateKey
     */
    protected $model;

    protected function setUp()
    {
        $this->storeManagerMock = $this->getMock('\Magento\Framework\StoreManagerInterface');
        $this->customerSessionMock = $this->getMock('\Magento\Customer\Model\Session', array(), array(), '', false);
        $this->queryFactoryMock = $this->getMock(
            '\Magento\CatalogSearch\Model\QueryFactory',
            array(),
            array(),
            '',
            false
        );

        $this->model = new StateKey($this->storeManagerMock, $this->customerSessionMock, $this->queryFactoryMock);
    }

    /**
     * @covers \Magento\Catalog\Model\Layer\Search\StateKey::toString
     * @covers \Magento\Catalog\Model\Layer\Search\StateKey::__construct
     */
    public function testToString()
    {
        $categoryMock = $this->getMock('\Magento\Catalog\Model\Category', array(), array(), '', false);
        $categoryMock->expects($this->once())->method('getId')->will($this->returnValue('1'));

        $storeMock = $this->getMock('\Magento\Store\Model\Store', array(), array(), '', false);
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue('2'));

        $this->customerSessionMock->expects($this->once())->method('getCustomerGroupId')->will($this->returnValue('3'));

        $queryMock = $this->getMock('\Magento\CatalogSearch\Helper\Query', array('getId'), array(), '', false);
        $queryMock->expects($this->once())->method('getId')->will($this->returnValue('4'));
        $this->queryFactoryMock->expects($this->once())->method('getQuery')->will($this->returnValue($queryMock));

        $this->assertEquals('Q_4_STORE_2_CAT_1_CUSTGROUP_3', $this->model->toString($categoryMock));
    }
}
