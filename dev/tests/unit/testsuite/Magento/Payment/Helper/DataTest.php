<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Payment\Helper\Data */
    protected $_helper;

    /**  @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_coreStoreConfig;

    /**  @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_methodFactory;

    protected function setUp()
    {
        $context                = $this->getMock('Magento\App\Helper\Context', [], [], '', false);
        $this->_coreStoreConfig = $this->getMock('Magento\Core\Model\Store\Config', [], [], '', false);
        $layout                 = $this->getMock('Magento\View\LayoutInterface', [], [], '', false);
        $this->_methodFactory   = $this->getMock('Magento\Payment\Model\Method\Factory', [], [], '', false);
        $config                 = $this->getMock('Magento\App\ConfigInterface', [], [], '', false);
        $appEmulation           = $this->getMock('Magento\Core\Model\App\Emulation', [], [], '', false);
        $paymentConfig          = $this->getMock('Magento\Payment\Model\Config', [], [], '', false);
        $initialConfig          = $this->getMock('Magento\App\Config\Initial', [], [], '', false);

        $this->_helper = new \Magento\Payment\Helper\Data(
            $context,
            $this->_coreStoreConfig,
            $layout,
            $this->_methodFactory,
            $config,
            $appEmulation,
            $paymentConfig,
            $initialConfig
        );
    }

    /**
     * @param string $code
     * @param string $class
     * @param string $methodInstance
     * @dataProvider getMethodInstanceDataProvider
     */
    public function testGetMethodInstance($code, $class, $methodInstance)
    {
        $this->_coreStoreConfig->expects(
            $this->once()
        )->method(
            'getConfig'
        )->will(
            $this->returnValue(
                $class
            )
        );
        $this->_methodFactory->expects(
            $this->any()
        )->method(
            'create'
        )->with(
            $class
        )->will(
            $this->returnValue(
                $methodInstance
            )
        );

        $this->assertEquals($methodInstance, $this->_helper->getMethodInstance($code));
    }

    public function getMethodInstanceDataProvider()
    {
        return array(
            ['method_code', 'method_class', 'method_instance'],
            ['method_code', false, false]
        );
    }
}
