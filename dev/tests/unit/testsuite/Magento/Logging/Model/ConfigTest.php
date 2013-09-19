<?php
/**
 * Test Magento_Logging_Model_Config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Model;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Logging\Model\Config\Data
     */
    protected $_storageMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Logging\Model\Config
     */
    protected $_model;

    /*
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Store
     */
    protected $_storeMock;

    public function setUp()
    {
        $this->_storageMock = $this->getMockBuilder('Magento\Logging\Model\Config\Data')
            ->setMethods(array('get'))
            ->disableOriginalConstructor()
            ->getMock();

        $loggingConfig = array(
            'actions' => array(
                'test_action_withlabel' => array(
                    'label' => 'Test Action Label'
                ),
                'test_action_withoutlabel' => array()
            ),
            'test' => array(
                'label' => 'Test Label'
            ),
            'configured_log_group' => array(
                'label' => 'Log Group With Configuration',
                'actions'=>
                array(
                    'adminhtml_checkout_index'=>
                    array(
                        'log_name' => 'configured_log_group',
                        'action' => 'view',
                        'expected_models' => array(
                            'Magento\Sales\Model\Quote' => array()
                        )
                    )
                )
            )
        );
        $this->_storageMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('logging'))
            ->will($this->returnValue($loggingConfig));

        $storeManagerMock = $this->getMockBuilder('Magento\Core\Model\StoreManager')
            ->setMethods(array('getStore'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->_store = $this->getMockBuilder('Magento\Core\Model\StoreManager')
            ->setMethods(array('getConfig'))
            ->disableOriginalConstructor()
            ->getMock();

        $storeManagerMock->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($this->_store));


        $this->_model = new \Magento\Logging\Model\Config($this->_storageMock, $storeManagerMock);
    }

    public function testLabels()
    {
        $expected = array(
            'test' => 'Test Label',
            'configured_log_group' => 'Log Group With Configuration'
        );
        $result = $this->_model->getLabels();
        $this->assertEquals($expected, $result);
    }

    public function testGetActionLabel()
    {
        $expected = 'Test Action Label';
        $result = $this->_model->getActionLabel('test_action_withlabel');
        $this->assertEquals($expected, $result);
    }

    public function testGetActionWithoutLabel()
    {
        $this->assertEquals('test_action_withoutlabel', $this->_model->getActionLabel('test_action_withoutlabel'));
        $this->assertEquals('nonconfigured_action', $this->_model->getActionLabel('nonconfigured_action'));
    }

    public function testGetSystemConfigValues()
    {
        $config = array(
            'enterprise_checkout' => 1,
            'customer' => 1
        );
        $this->_store->expects($this->once())
            ->method('getConfig')
            ->with($this->equalTo('admin/magento_logging/actions'))
            ->will($this->returnValue(serialize($config)));
        $this->assertEquals($config, $this->_model->getSystemConfigValues());
    }

    public function testGetSystemConfigValuesNegative()
    {
        $expected = array(
            'test' => 1,
            'configured_log_group' => 1
        );
        $this->_store->expects($this->once())
            ->method('getConfig')
            ->with($this->equalTo('admin/magento_logging/actions'))
            ->will($this->returnValue(null));
        $this->assertEquals($expected, $this->_model->getSystemConfigValues());
    }

    public function testHasSystemConfigValues()
    {
        $config = array(
            'enterprise_checkout' => 1,
            'customer' => 1
        );

        $this->_store->expects($this->once())
            ->method('getConfig')
            ->with($this->equalTo('admin/magento_logging/actions'))
            ->will($this->returnValue(serialize($config)));

        $this->assertTrue($this->_model->hasSystemConfigValue('enterprise_checkout'));
        $this->assertFalse($this->_model->hasSystemConfigValue('enterprise_catalogevent'));
    }

    public function testIsEventGroupLogged()
    {
        $config = array(
            'enterprise_checkout' => 1,
            'customer' => 1
        );

        $this->_store->expects($this->once())
            ->method('getConfig')
            ->with($this->equalTo('admin/magento_logging/actions'))
            ->will($this->returnValue(serialize($config)));

        $this->assertTrue($this->_model->isEventGroupLogged('enterprise_checkout'));
        $this->assertFalse($this->_model->isEventGroupLogged('enterprise_catalogevent'));
    }

    public function testGetEventByFullActionName()
    {
        $expected = array(
            'log_name' => 'configured_log_group',
            'action' => 'view',
            'expected_models' => array(
                'Magento\Sales\Model\Quote' => array()
            )
        );
        $this->assertEquals($expected, $this->_model->getEventByFullActionName('adminhtml_checkout_index'));
    }
}
