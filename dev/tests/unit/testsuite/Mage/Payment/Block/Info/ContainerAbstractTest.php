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

class Mage_Payment_Block_Info_ContainerAbstractTest extends PHPUnit_Framework_TestCase
{
    public function testSetInfoTemplate()
    {
        include '_files/container_descendant.php';

        $childBlock = new Mage_Core_Block_Template;

        $block = $this->getMock('Mage_Payment_Block_Info_Container_Descendant', array('getChildBlock'));
        $block->expects($this->atLeastOnce())
            ->method('getChildBlock')
            ->with('payment.info.method_descendant')
            ->will($this->returnValue($childBlock));

        $template = 'any_template.phtml';
        $this->assertNotEquals($template, $childBlock->getTemplate());
        $block->setInfoTemplate('method_descendant', $template);
        $this->assertEquals($template, $childBlock->getTemplate());
    }
}
