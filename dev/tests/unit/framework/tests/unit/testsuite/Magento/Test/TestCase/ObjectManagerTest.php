<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_TestCase_ObjectManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Magento_Test_TestCase_ObjectManager::getBlock
     */
    public function testGetBlock()
    {
        $objectManager = new Magento_Test_TestCase_ObjectManager();
        /** @var $template Mage_Core_Block_Template */
        $template = $objectManager->getBlock('Mage_Core_Block_Template');
        $this->assertInstanceOf('Mage_Core_Block_Template', $template);
        $this->assertInstanceOf('Mage_Core_Model_Layout', $template->getLayout());

        /** @var $layoutMock Mage_Core_Model_Layout */
        $layoutMock = $this->getMock('Mage_Core_Model_Layout', array('getArea'), array(), '', false);
        $layoutMock->expects($this->once())
            ->method('getArea')
            ->will($this->returnValue('frontend'));

        $arguments = array('layout' => $layoutMock);
        /** @var $template Mage_Core_Block_Template */
        $template = $objectManager->getBlock('Mage_Core_Block_Template', $arguments);
        $this->assertEquals('frontend', $template->getArea());
    }

    /**
     * @covers Magento_Test_TestCase_ObjectManager::getModel
     */
    public function testGetModel()
    {
        $objectManager = new Magento_Test_TestCase_ObjectManager();
        /** @var $model Mage_Core_Model_Config_Data */
        $model = $objectManager->getModel('Mage_Core_Model_Config_Data');
        $this->assertInstanceOf('Mage_Core_Model_Config_Data', $model);
        $this->assertInstanceOf('Mage_Core_Model_Resource_Resource', $model->getResource());

        /** @var $resourceMock Mage_Core_Model_Resource_Resource */
        $resourceMock = $this->getMock('Mage_Core_Model_Resource_Resource', array('_getReadAdapter', 'getIdFieldName'),
            array(), '', false
        );
        $resourceMock->expects($this->once())
            ->method('_getReadAdapter')
            ->will($this->returnValue(false));
        $resourceMock->expects($this->any())
            ->method('getIdFieldName')
            ->will($this->returnValue('id'));
        $arguments = array('resource' => $resourceMock);
        $model = $objectManager->getModel('Mage_Core_Model_Config_Data', $arguments);
        $this->assertFalse($model->getResource()->getDataVersion('test'));
    }
}
