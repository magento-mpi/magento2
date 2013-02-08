<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_ObjectManager_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_ObjectManager_Config
     */
    protected $_model;

    protected function setUp()
    {
        $params = array(
        );
        $this->_model = new Mage_Core_Model_ObjectManager_Config($params);
    }

    public function testConfigureInitializedObjectManager()
    {
        $configuration = $this->getMock('stdClass', array('asArray'));
        $configuration->expects($this->any())->method('asArray')->will($this->returnValue(array('configuratorClass')));
        $configMock = $this->getMock('Mage_Core_Model_Config_Primary', array(), array(), '', false);
        $configMock->expects($this->any())->method('getNode')->with($this->stringStartsWith('global'))
            ->will($this->returnValue($configuration));
        $objectManagerMock = $this->getMock('Magento_ObjectManager');
        $objectManagerMock->expects($this->exactly(2))->method('setConfiguration');
        $objectManagerMock->expects($this->once())->method('get')->with('Mage_Core_Model_Config_Primary')
            -> will($this->returnValue($configMock));
        $configuratorMock = $this->getMock('Magento_ObjectManager_Configuration');
        $configuratorMock->expects($this->once())->method('configure')->with($objectManagerMock);
        $objectManagerMock->expects($this->once())->method('create')->with('configuratorClass')
            ->will($this->returnValue($configuratorMock));
        $this->_model->configure($objectManagerMock);
    }
}
