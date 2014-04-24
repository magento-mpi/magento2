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
     * @var \Magento\Logging\Model\Handler\Controllers
     */
    protected $object;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Logging\Model\Event\ChangesFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventChangesFactory;

    /**
     * @var \Magento\Logging\Model\Event\Changes|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventChanges;

    /**
     * @var \Magento\Backend\Model\Config\Structure|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configStructure;

    /**
     * @var \Magento\Logging\Model\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $processor;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->request = $this->getMock('Magento\Framework\App\Request\Http', array(), array(), '', false);
        $this->request->expects($this->any())->method('getParams')->will($this->returnValue(array()));

        $this->eventChanges = new \Magento\Framework\Object();
        $this->eventChangesFactory = $this->getMock(
            '\Magento\Logging\Model\Event\ChangesFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->eventChangesFactory->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->eventChanges)
        );

        $this->configStructure = $this->getMock(
            '\Magento\Backend\Model\Config\Structure',
            array('getFieldPathsByAttribute'),
            array(),
            '',
            false
        );
        $this->configStructure->expects(
            $this->any()
        )->method(
            'getFieldPathsByAttribute'
        )->will(
            $this->returnValue(array())
        );

        $this->object = $objectManager->getObject(
            'Magento\Logging\Model\Handler\Controllers',
            array(
                'request' => $this->request,
                'eventChangesFactory' => $this->eventChangesFactory,
                'structureConfig' => $this->configStructure
            )
        );

        $this->processor = $this->getMock('Magento\Logging\Model\Processor', array(), array(), '', false);
    }

    /**
     * @dataProvider postDispatchReportDataProvider
     */
    public function testPostDispatchReport($config, $expectedInfo)
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $eventModel = $helper->getObject('Magento\Logging\Model\Event');
        $processor = $this->getMockBuilder('Magento\Logging\Model\Processor')->disableOriginalConstructor()->getMock();

        $result = $this->object->postDispatchReport($config, $eventModel, $processor);
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
            array(array('controller_action' => 'reports_report_shopcart_product'), 'shopcart_product'),
            array(array('controller_action' => 'some_another_value'), false)
        );
    }

    /**
     * Assure that method works when post data contains group without ['fields'] key
     */
    public function testPostDispatchConfigSaveGroupWithoutFieldsKey()
    {
        $this->request->expects(
            $this->once()
        )->method(
            'getPost'
        )->will(
            $this->returnValue(array('groups' => array('name' => array())))
        );

        $this->assertEquals(
            array('info' => 'general'),
            $this->object->postDispatchConfigSave(array(), new \Magento\Framework\Object(), $this->processor)->getData()
        );
    }
}
