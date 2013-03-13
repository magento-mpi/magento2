<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_ObjectManagerTest extends PHPUnit_Framework_TestCase
{
    public function testConstructConfiguresObjectManager()
    {
        $this->assertNull(Mage::getObjectManager());
        $configMock = $this->getMock('Mage_Core_Model_Config_Primary', array(), array(), '', false);
        $configMock->expects($this->once())
            ->method('configure')
            ->with($this->isInstanceOf('Mage_Core_Model_ObjectManager'));
        $diMock = $this->getMock('Magento_Di');
        $imMock = $this->getMock('Magento_Di_InstanceManager');
        $diMock->expects($this->any())->method('instanceManager')->will($this->returnValue($imMock));
        $definition = new Magento_ObjectManager_Definition_Runtime();
        $objectManager = new Mage_Core_Model_ObjectManager($definition, $configMock);
        $this->assertSame($configMock, $objectManager->get('Mage_Core_Model_Config_Primary'));
    }
}
