<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PbridgePaypal\Model\Payment\Method;

class PaypalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Paypal
     */
    protected $_model;

    /**
     * @var \Magento\Payment\Model\Method\Cc|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_methodInstance;

    /**
     * @var \Magento\Payment\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_paymentData;

    /**
     * @var \Magento\Pbridge\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_pbridgeData;

    /**
     * @var \Magento\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopeConfig;

    protected function assertPreConditions()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_methodInstance = $this->getMock('Magento\Payment\Model\Method\Cc', array(), array(), '', false);
        $this->_paymentData = $this->getMock('Magento\Payment\Helper\Data', array(), array(), '', false);
        $this->_pbridgeData = $this->getMock('Magento\Pbridge\Helper\Data', array(), array(), '', false);
        $this->_scopeConfig = $this->getMock('Magento\App\Config\ScopeConfigInterface');
        $paymentFactory = $this->getMock('Magento\Payment\Model\Method\Factory', array('create'), array(), '', false);
        $paymentFactory->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'paypal class name'
        )->will(
            $this->returnValue($this->_methodInstance)
        );
        $this->_model = $helper->getObject(
            'Magento\PbridgePaypal\Model\Payment\Method\Paypal',
            array(
                'paymentFactory' => $paymentFactory,
                'paypalClassName' => 'paypal class name',
                'paymentData' => $this->_paymentData,
                'pbridgeData' => $this->_pbridgeData,
                'scopeConfig' => $this->_scopeConfig
            )
        );
    }

    public function testMagicCall()
    {
        $this->_methodInstance->expects(
            $this->at(0)
        )->method(
            '__call'
        )->with(
            'anyMethod',
            array('args')
        )->will(
            $this->returnSelf()
        );
        $this->_methodInstance->expects(
            $this->at(1)
        )->method(
            '__call'
        )->with(
            'anyOtherMethod',
            array('other args')
        )->will(
            $this->returnValue('some value')
        );
        $this->assertSame($this->_model, $this->_model->anyMethod('args'));
        $this->assertSame('some value', $this->_model->anyOtherMethod('other args'));
    }

    public function testIsDummy()
    {
        $this->assertTrue($this->_model->getIsDummy());
    }

    public function testGetIsCentinelValidationEnabled()
    {
        $this->assertFalse($this->_model->getIsCentinelValidationEnabled());
    }

    public function testGetPbridgeMethodInstance()
    {
        $pbridgeMethodInstance = $this->_preparePbridgeMethodInstance();
        $this->assertEquals($pbridgeMethodInstance, $this->_model->getPbridgeMethodInstance());
        $this->assertEquals($pbridgeMethodInstance, $this->_model->getPbridgeMethodInstance());
    }

    public function testGetCode()
    {
        $this->_methodInstance->expects($this->once())->method('getCode')->will($this->returnValue('some_code'));
        $this->assertEquals('pbridge_some_code', $this->_model->getCode());
    }

    public function testGetOriginalCode()
    {
        $this->_methodInstance->expects($this->once())->method('getCode')->will($this->returnValue('some_code'));
        $this->assertEquals('some_code', $this->_model->getOriginalCode());
    }

    public function testAssignData()
    {
        $pbridgeMethodInstance = $this->_preparePbridgeMethodInstance();
        $pbridgeMethodInstance->expects($this->once())->method('assignData')->with('some data');
        $this->assertSame($this->_model, $this->_model->assignData('some data'));
    }

    public function testValidate()
    {
        $pbridgeMethodInstance = $this->_preparePbridgeMethodInstance();
        $pbridgeMethodInstance->expects($this->once())->method('validate');
        $this->assertSame($this->_model, $this->_model->validate());
    }

    public function testGetFormBlockType()
    {
        $this->_methodInstance->expects(
            $this->once()
        )->method(
            'getFormBlockType'
        )->will(
            $this->returnValue('some value')
        );
        $this->assertEquals('some value', $this->_model->getFormBlockType());
    }

    public function testGetTitle()
    {
        $this->_methodInstance->expects($this->once())->method('getTitle')->will($this->returnValue('some value'));
        $this->assertEquals('some value', $this->_model->getTitle());
    }

    /**
     * Prepare pbridge method instance
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _preparePbridgeMethodInstance()
    {
        $pbridgeMethodInstance = $this->getMock(
            'Magento\Pbridge\Model\Payment\Method\Pbridge',
            array(),
            array(),
            '',
            false
        );
        $pbridgeMethodInstance->expects(
            $this->once()
        )->method(
            'setOriginalMethodInstance'
        )->with(
            $this->identicalTo($this->_model)
        );
        $this->_paymentData->expects(
            $this->once()
        )->method(
            'getMethodInstance'
        )->with(
            'pbridge'
        )->will(
            $this->returnValue($pbridgeMethodInstance)
        );
        return $pbridgeMethodInstance;
    }

    public function testSetStoreObject()
    {
        $store = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $store->expects($this->once())->method('getId')->will($this->returnValue('store id'));
        $this->_methodInstance->expects($this->once())->method('setData')->with('store', $store);
        $this->_pbridgeData->expects($this->once())->method('setStoreId')->with('store id');
        $this->_methodInstance->expects($this->once())->method('__call')->with('setStore', array($store));
        $this->assertSame($this->_model, $this->_model->setStore($store));
    }

    public function testSetStore()
    {
        $store = 'store id';
        $this->_methodInstance->expects($this->once())->method('setData')->with('store', $store);
        $this->_pbridgeData->expects($this->once())->method('setStoreId')->with('store id');
        $this->_methodInstance->expects($this->once())->method('__call')->with('setStore', array($store));
        $this->assertSame($this->_model, $this->_model->setStore($store));
    }

    /**
     * @param string|null $storeId
     * @dataProvider getConfigDataDataProvider
     */
    public function testGetConfigData($storeId)
    {
        $newStoreId = $storeId;
        if (!isset($storeId)) {
            $newStoreId = 'some store id';
            $this->_methodInstance->expects(
                $this->once()
            )->method(
                '__call'
            )->with(
                'getStore'
            )->will(
                $this->returnValue($newStoreId)
            );
        }
        $this->_methodInstance->expects($this->once())->method('getCode')->will($this->returnValue('some_code'));
        $path = 'payment/some_code/some_field';
        $this->_scopeConfig->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $newStoreId
        )->will(
            $this->returnValue('config value')
        );
        $this->assertEquals('config value', $this->_model->getConfigData('some_field', $storeId));
    }

    public function getConfigDataDataProvider()
    {
        return array(array(null), array('any store id'));
    }
}
