<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_Reward_Model_Observer_PlaceOrder_Restriction_BackendTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\Observer\PlaceOrder\Restriction\Backend
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_authorizationMock;

    protected function setUp()
    {
        $this->_helper = $this->getMock('Magento\Reward\Helper\Data', array(), array(), '', false);
        $this->_authorizationMock = $this->getMock('Magento\AuthorizationInterface');
        $this->_model = new \Magento\Reward\Model\Observer\PlaceOrder\Restriction\Backend(
            $this->_helper,
            $this->_authorizationMock
        );
    }

    /**
     * @dataProvider testIsAllowedDataProvider
     * @param $expectedResult
     * @param $isEnabled
     * @param $isAllowed
     */
    public function testIsAllowed($expectedResult, $isEnabled, $isAllowed)
    {
        $this->_helper->expects($this->once())->method('isEnabledOnFront')->will($this->returnValue($isEnabled));
        $this->_authorizationMock->expects($this->any())->method('isAllowed')->will($this->returnValue($isAllowed));
        $this->assertEquals($expectedResult, $this->_model->isAllowed());
    }

    public function testIsAllowedDataProvider()
    {
        return array(
            array(true, true, true),
            array(false, true, false),
            array(false, false, false)
        );
    }
}
