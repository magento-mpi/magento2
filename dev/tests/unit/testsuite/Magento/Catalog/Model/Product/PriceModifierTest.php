<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product;

class PriceModifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\PriceModifier
     */
    protected $priceModifier;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var array
     */
    protected $prices = array();

    protected function setUp()
    {
        $this->productMock =
            $this->getMock('Magento\Catalog\Model\Product',
                array('getData', 'setData', '__wakeup'), array(), '', false);
        $this->priceModifier = new \Magento\Catalog\Model\Product\PriceModifier();
        $this->prices = array(
            0 => array(
                'all_groups' => 0,
                'cust_group' => 1,
                'price_qty' => 15,
                'website_id' => 1
            ),
            1 => array(
                'all_groups' => 1,
                'cust_group' => 0,
                'price_qty' => 10,
                'website_id' => 1
            )
        );
    }

    public function testSuccessfullyRemoveGroupPriceSpecifiedForOneGroup()
    {
        $this->productMock
            ->expects($this->once())
            ->method('getData')
            ->with('group_price')
            ->will($this->returnValue($this->prices));
        $expectedPrices = array(1 => $this->prices[1]);
        $this->productMock->expects($this->once())->method('setData')->with('group_price', $expectedPrices);
        $this->priceModifier->removeGroupPrice($this->productMock, 1, 1);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedMessage This product doesn't have group price
     */
    public function testRemoveWhenGroupPricesNotExists()
    {
        $this->productMock
            ->expects($this->once())
            ->method('getData')
            ->with('group_price')
            ->will($this->returnValue(array()));
        $this->productMock->expects($this->never())->method('setData');
        $this->priceModifier->removeGroupPrice($this->productMock, 1, 1);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedMessage For current  customerGroupId = '10' any group price exist'.
     */
    public function testRemoveGroupPriceForNonExistingCustomerGroup()
    {
        $this->productMock
            ->expects($this->once())
            ->method('getData')
            ->with('group_price')
            ->will($this->returnValue($this->prices));
        $this->productMock->expects($this->never())->method('setData');
        $this->priceModifier->removeGroupPrice($this->productMock, 10, 1);
    }
}
