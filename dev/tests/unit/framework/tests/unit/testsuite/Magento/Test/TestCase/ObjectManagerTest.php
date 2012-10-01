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
}
