<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Block;

class InfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManager;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_storeManager = $this->getMockBuilder(
            '\Magento\Store\Model\StoreManager'
        )->setMethods(
            array('getStore')
        )->disableOriginalConstructor()->getMock();
        $context = $helper->getObject(
            'Magento\Framework\View\Element\Template\Context',
            array('storeManager' => $this->_storeManager)
        );
        $this->_object = $helper->getObject('Magento\Payment\Block\Info', array('context' => $context));
    }

    /**
     * @dataProvider getIsSecureModeDataProvider
     * @param bool $isSecureMode
     * @param bool $methodInstance
     * @param bool $store
     * @param string $storeCode
     * @param bool $expectedResult
     */
    public function testGetIsSecureMode($isSecureMode, $methodInstance, $store, $storeCode, $expectedResult)
    {
        if (isset($store)) {
            $methodInstance = $this->_getMethodInstanceMock($store);
        }

        if (isset($storeCode)) {
            $storeMock = $this->_getStoreMock($storeCode);
            $this->_storeManager->expects($this->any())->method('getStore')->will($this->returnValue($storeMock));
        }

        $paymentInfo = $this->getMockBuilder('\Magento\Payment\Model\Info')->disableOriginalConstructor()->getMock();
        $paymentInfo->expects($this->any())->method('getMethodInstance')->will($this->returnValue($methodInstance));

        $this->_object->setData('info', $paymentInfo);
        $this->_object->setData('is_secure_mode', $isSecureMode);
        $result = $this->_object->getIsSecureMode();
        $this->assertEquals($result, $expectedResult);
    }

    public function getIsSecureModeDataProvider()
    {
        return array(
            array(false, true, null, null, false),
            array(true, true, null, null, true),
            array(null, false, null, null, true),
            array(null, null, false, null, false),
            array(null, null, true, 'default', true),
            array(null, null, true, 'admin', false)
        );
    }

    /**
     * @param bool $store
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMethodInstanceMock($store)
    {
        $methodInstance = $this->getMockBuilder(
            '\Magento\Payment\Model\Method\AbstractMethod'
        )->setMethods(
            array('getStore')
        )->disableOriginalConstructor()->getMock();
        $methodInstance->expects($this->any())->method('getStore')->will($this->returnValue($store));
        return $methodInstance;
    }

    /**
     * @param string $storeCode
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getStoreMock($storeCode)
    {
        $storeMock = $this->getMockBuilder('\Magento\Store\Model\Store')->disableOriginalConstructor()->getMock();
        $storeMock->expects($this->any())->method('getCode')->will($this->returnValue($storeCode));
        return $storeMock;
    }
}
