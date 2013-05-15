<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Saas_Model_ObjectManager_ConfiguratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Saas_Model_ObjectManager_Configurator
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_taskNamePrefix = 'taskNamePrefix';

    protected function setUp()
    {
        $this->_model = new Saas_Saas_Model_ObjectManager_Configurator(array(
            'maintenance_mode' => array(),
            'task_name_prefix' => $this->_taskNamePrefix,
        ));
    }

    public function testConfigure()
    {
        $objectManagerMock = $this->getMock('Magento_ObjectManager');
        $objectManagerMock->expects($this->once())
            ->method('configure')
            ->with(array(
                'Saas_Saas_Model_Maintenance_Config' => array(
                    'parameters' => array('config' => array()),
                ),
                'Saas_Saas_Model_DisabledConfiguration_Config' => array(
                    'parameters' => array('plainList' => Saas_Saas_Model_DisabledConfiguration_Config::getPlainList()),
                ),
                'Enterprise_Queue_Model_Queue' => array(
                    'parameters' => array('taskNamePrefix' => $this->_taskNamePrefix),
                ),
            ));
        $this->_model->configure($objectManagerMock);
    }
}
