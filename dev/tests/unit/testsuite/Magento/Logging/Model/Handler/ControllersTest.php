<?php
/**
 * Test \Magento\Logging\Model\Config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Model\Handler;

class ControllersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Logging\Model\Handler\Controllers
     */
    protected $_model;

    public function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $requestMock->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue(array()));
        $this->_model = $helper->getObject(
            'Magento\Logging\Model\Handler\Controllers',
            array('request' => $requestMock)
        );
    }

    /**
     * @dataProvider postDispatchReportDataProvider
     */
    public function testPostDispatchReport($config, $expectedInfo)
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $eventModel = $helper->getObject('Magento\Logging\Model\Event');
        $processor = $this->getMockBuilder('Magento\Logging\Model\Processor')
            ->disableOriginalConstructor()
            ->getMock();
        ;

        $result = $this->_model->postDispatchReport($config, $eventModel, $processor);
        if (is_object($result)) {
            $result = $result->getInfo();
        }
        $this->assertEquals($expectedInfo, $result);
    }

    /**
     * @return array
     */
    public function postDispatchReportDataProvider()
    {
        return array(
            array(
            array('controller_action' => 'reports_report_shopcart_product'),
            'shopcart_product',
        ),
            array(
                array('controller_action' => 'some_another_value'),
                false,
            )
        );
    }
}
