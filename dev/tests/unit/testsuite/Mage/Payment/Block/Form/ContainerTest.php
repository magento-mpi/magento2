<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Payment
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Payment_Block_Form_ContainerAbstractTest extends PHPUnit_Framework_TestCase
{
    public function testSetMethodFormTemplate()
    {
        $this->markTestIncomplete('Test incompleted after DI introduction');
        $childBlockA = new Mage_Core_Block_Template(
            $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Layout', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Translate', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Design_Package', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Session', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Store_Config', array(), array(), '', false)
        );
        $childBlockB = new Mage_Core_Block_Template(
            $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Layout', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Translate', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Design_Package', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Session', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Store_Config', array(), array(), '', false)
        );

        $func = function ($blockName) use ($childBlockA, $childBlockB) {
            switch ($blockName) {
                case 'payment.method.a':
                    return $childBlockA;
                case 'payment.method.b':
                    return $childBlockB;
            }
            return null;
        };

        $block = $this->getMock('Mage_Payment_Block_Form_Container', array('getChildBlock'));
        $block->expects($this->atLeastOnce())
            ->method('getChildBlock')
            ->will($this->returnCallback($func));

        $template = 'any_template.phtml';
        $this->assertNotEquals($template, $childBlockA->getTemplate());
        $this->assertNotEquals($template, $childBlockB->getTemplate());

        $block->setMethodFormTemplate('a', $template);
        $this->assertEquals($template, $childBlockA->getTemplate()); // Template is set to the block
        $this->assertNotEquals($template, $childBlockB->getTemplate()); // Template is not propagated to other blocks
    }
}
