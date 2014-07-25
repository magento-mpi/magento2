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
    protected $_scopeConfig;

    /**  @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_initialConfig;

    /**  @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_methodFactory;

    protected function setUp()
    {
        $context              = $this->getMock('Magento\Framework\App\Helper\Context', [], [], '', false);
        $this->_scopeConfig   = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface', [], [], '', false);
        $layout               = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);
        $this->_methodFactory = $this->getMock('Magento\Payment\Model\Method\Factory', [], [], '', false);
        $appEmulation         = $this->getMock('Magento\Core\Model\App\Emulation', [], [], '', false);
        $paymentConfig        = $this->getMock('Magento\Payment\Model\Config', [], [], '', false);
        $this->_initialConfig = $this->getMock('Magento\Framework\App\Config\Initial', [], [], '', false);

        $this->_helper = new \Magento\Payment\Helper\Data(
            $context,
            $this->_scopeConfig,
            $layout,
            $this->_methodFactory,
            $appEmulation,
            $paymentConfig,
            $this->_initialConfig
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
        $this->_scopeConfig->expects(
            $this->once()
        )->method(
            'getValue'
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

    /**
     * @param $method1 array
     * @param $method2 array
     *
     * @dataProvider getSortMethodsDataProvider
     */
    public function testSortMethods($method1, $method2)
    {
        $this->_initialConfig->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(
                array(\Magento\Payment\Helper\Data::XML_PATH_PAYMENT_METHODS => array(
                    'method1' => $method1,
                    'method2 '=> $method2
                ))
            ));

        $this->_scopeConfig->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue('Magento\Payment\Model\Method\AbstractMethod'));

        $methodInstanceMock1 = $this->getMock(
            'Magento\Framework\Object',
            array('isAvailable','getConfigData'),
            array(),
            '',
            false
        );
        $methodInstanceMock1->expects($this->any())
            ->method('isAvailable')
            ->will($this->returnValue(true));
        $methodInstanceMock1->expects($this->any())
            ->method('getConfigData')
            ->will($this->returnValue($method1['sort_order']));

        $methodInstanceMock2 = $this->getMock(
            'Magento\Framework\Object',
            array('isAvailable','getConfigData'),
            array(),
            '',
            false
        );
        $methodInstanceMock2->expects($this->any())
            ->method('isAvailable')
            ->will($this->returnValue(true));
        $methodInstanceMock2->expects($this->any())
            ->method('getConfigData')
            ->will($this->returnValue($method2['sort_order']));

        $this->_methodFactory->expects($this->at(0))
            ->method('create')
            ->will($this->returnValue($methodInstanceMock1));

        $this->_methodFactory->expects($this->at(1))
            ->method('create')
            ->will($this->returnValue($methodInstanceMock2));

        $sortedMethods = $this->_helper->getStoreMethods();
        $this->assertTrue(array_shift($sortedMethods)->getSortOrder() < array_shift($sortedMethods)->getSortOrder());
    }


    public function getMethodInstanceDataProvider()
    {
        return array(
            ['method_code', 'method_class', 'method_instance'],
            ['method_code', false, false]
        );
    }

    public function getSortMethodsDataProvider()
    {
        return array(
            array(
                array('sort_order' => 0),
                array('sort_order' => 1)
            ),
            array(
                array('sort_order' => 2),
                array('sort_order' => 1),
            )
        );
    }
}
