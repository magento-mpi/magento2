<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\AbstractModel
     */
    protected $_order;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Payment\Model\CartFactory
     */
    protected $_cartFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Paypal\Model\CartFactory
     */
    protected $_paypalCartFactory;

    /**
     * setUp
     */
    protected function setUp()
    {
        $this->_cartFactory = $this->getMock('Magento\Payment\Model\CartFactory', ['create'], [], '', false);
        $this->_paypalCartFactory = $this->getMock('Magento\Paypal\Model\CartFactory', ['create'], [], '', false);
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject(
            'Magento\Pbridge\Helper\Data',
            ['cartFactory' => $this->_cartFactory, 'paypalCartFactory' => $this->_paypalCartFactory]
        );
        $this->_order = $this->getMock('Magento\Core\Model\AbstractModel', array(), array(), '', false);
    }

    public function testPrepareCart()
    {
        $expected = $this->_prepareCartExpectations($this->_cartFactory);
        $this->assertEquals($expected, $this->_model->prepareCart($this->_order));
    }

    /**
     * Prepare cart factory expectations
     *
     * @param \PHPUnit_Framework_MockObject_MockObject|\Magento\Payment\Model\CartFactory $cartFactory
     * @return array
     */
    public function _prepareCartExpectations($cartFactory)
    {
        $items = [new \Magento\Object([
            'parent_item' => 'parent',
            'name' => 'name',
            'qty' => '1',
            'price' => '12.2',
            'original_item' => new \Magento\Object()
        ])];
        $cart = $this->getMock('Magento\Payment\Model\Cart', [], [], '', false);
        $cart->expects($this->once())
            ->method('getAmounts')
            ->will($this->returnValue(['28']));
        $cart->expects($this->once())
            ->method('getAllItems')
            ->will($this->returnValue($items));
        $cartFactory->expects($this->once())
            ->method('create')
            ->with(array('salesModel' => $this->_order))
            ->will($this->returnValue($cart));
        return array_merge(['items' => [$items[0]->getData()]], ['28']);
    }
}
