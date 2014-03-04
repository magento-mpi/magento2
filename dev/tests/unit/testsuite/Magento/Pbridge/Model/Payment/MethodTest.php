<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Model\Payment;

class MethodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Pbridge\Model\Payment\Method|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var \ReflectionProperty
     */
    protected $_allowCurrencyCode;

    /**
     * @var \ReflectionProperty
     */
    protected $_paymentCode;

    /**
     * setUp
     */
    protected function setUp()
    {
        $config = $this->getMockBuilder('Magento\Core\Model\Store\Config')
            ->disableOriginalConstructor()
            ->setMethods(array('getConfig'))
            ->getMock();
        $config->expects($this->any())
            ->method('getConfig')
            ->with('payment/code/currency', 0)
            ->will($this->returnValue('BTN'));

        $paymentHelper = $this->getMockBuilder('Magento\Payment\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(array('getMethodInstance'))
            ->getMock();
        $paymentHelper->expects($this->any())
            ->method('getMethodInstance')
            ->with('pbridge')
            ->will($this->returnValue(new \Magento\Object()));

        $this->_model = new \Magento\Pbridge\Model\Payment\Method(
            $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false),
            $paymentHelper,
            $config,
            $this->getMock('Magento\Logger\AdapterFactory', array(), array(), '', false),
            $this->getMock('Magento\Logger', array(), array(), '', false),
            $this->getMock('Magento\Module\ModuleListInterface', array(), array(), '', false),
            $this->getMock('Magento\LocaleInterface', array(), array(), '', false),
            $this->getMock('Magento\Centinel\Model\Service', array(), array(), '', false),
            $this->getMock('Magento\Pbridge\Helper\Data', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\StoreManagerInterface', array(), array(), '', false),
            'getFormBlockType',
            array()
        );

        $this->_allowCurrencyCode = new \ReflectionProperty(
            'Magento\Pbridge\Model\Payment\Method',
            '_allowCurrencyCode'
        );
        $this->_allowCurrencyCode->setAccessible(true);
        $this->_paymentCode = new \ReflectionProperty('Magento\Payment\Model\Method\AbstractMethod', '_code');
        $this->_paymentCode->setAccessible(true);
    }

    public function testCanUseForCurrency()
    {
        $this->assertTrue($this->_model->canUseForCurrency('UAH'));
        $this->_allowCurrencyCode->setValue($this->_model, array('USD', 'EUR'));
        $this->_model->setData('_accepted_currency', array('USD', 'EUR'));
        $this->assertFalse($this->_model->canUseForCurrency('UAH'));
    }

    public function testGetAcceptedCurrencyCodes()
    {
        $this->_allowCurrencyCode->setValue($this->_model, array('USD', 'EUR'));
        $this->_paymentCode->setValue($this->_model, 'code');
        $this->assertEquals(array('USD', 'EUR', 'BTN'), $this->_model->getAcceptedCurrencyCodes());
        $this->_model->setData('_accepted_currency', array('USD', 'EUR'));
        $this->assertEquals(array('USD', 'EUR'), $this->_model->getAcceptedCurrencyCodes());
    }

    public function testGetIsDummy()
    {
        $this->assertTrue($this->_model->getIsDummy());
    }

    public function testGetPbridgeMethodInstance()
    {
        $this->assertInstanceOf('\Magento\Object', $this->_model->getPbridgeMethodInstance());
    }

    public function testGetOriginalCode()
    {
        $this->_paymentCode->setValue($this->_model, 'code');
        $this->assertEquals('code', $this->_model->getOriginalCode());
    }

    public function testGetFormBlockType()
    {
        $this->assertEquals('getFormBlockType', $this->_model->getFormBlockType());
    }

    public function testGetIsCentinelValidationEnabled()
    {
        $this->assertFalse($this->_model->getIsCentinelValidationEnabled());
    }
}
