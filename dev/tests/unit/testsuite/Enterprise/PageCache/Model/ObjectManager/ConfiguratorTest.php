<?php
    /**
     * {license_notice}
     *
     *
     * @copyright   {copyright}
     * @license     {license_link}
     */

    /**
     * Test class for Enterprise_PageCache_Model_ObjectManager_Configurator
     */
class Enterprise_PageCache_Model_ObjectManager_ConfiguratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_PageCache_Model_ObjectManager_Configurator
     */
    protected $_model;

    protected function setUp()
    {
        $params = array(
            Mage::PARAM_RUN_CODE => 'run_code',
        );
        $this->_model = new Enterprise_PageCache_Model_ObjectManager_Configurator($params);
    }

    public function testConfigure()
    {
        $objectManager = $this->getMock('Magento_ObjectManager',
            array('addSharedInstance', 'get', 'create', 'loadAreaConfiguration', 'configure'),
            array(), '', false, false
        );
        $factoryMock = $this->getMock('Enterprise_PageCache_Model_CacheFactory', array(), array(), '', false, false);
        $configMock = $this->getMock('Mage_Core_Model_Config_Primary', array(), array(), '', false, false);
        $dirsMock = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false, false);
        $helperMock = $this->getMock('Mage_Core_Model_Factory_Helper', array(), array(), '', false, false);

        $valueMap = array(
            array('Enterprise_PageCache_Model_CacheFactory', array(), $factoryMock),
            array('Mage_Core_Model_Config_Primary', array(), $configMock),
            array('Mage_Core_Model_Dir', array(), $dirsMock),
            array('Mage_Core_Model_Factory_Helper', array(), $helperMock),
        );

        $objectManager->expects($this->exactly(4))->method('get')->will($this->returnValueMap($valueMap));

        $arguments = array(
            'config' => $configMock,
            'dirs' => $dirsMock,
            'helperFactory' => $helperMock,
            'banCache' => false,
            'options' => array(),
        );

        $factoryMock->expects($this->once())->method('get')->with($arguments);
        $objectManager->expects($this->exactly(2))->method('addSharedInstance');

        $expectedParams = array(
            'Enterprise_PageCache_Model_Processor' => array(
                'parameters' => array('scopeCode' => 'run_code'),
            ));
        $objectManager->expects($this->once())->method('configure')->with($expectedParams);
        $this->_model->configure($objectManager);
    }

}
