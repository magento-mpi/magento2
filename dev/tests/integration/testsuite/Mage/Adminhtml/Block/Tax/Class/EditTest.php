<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Tax_Class_EditTest extends PHPUnit_Framework_TestCase
{
    public function testSetClassType()
    {
        $layout = new Mage_Core_Model_Layout();
        $block = $layout->createBlock('Mage_Adminhtml_Block_Tax_Class_Edit', 'block');
        $childBlock = $layout->addBlock('Mage_Core_Block_Template', 'form', 'block');

        $expected = 'a_class_type';
        $this->assertNotEquals($expected, $childBlock->getClassType());
        $block->setClassType($expected);
        $this->assertEquals($expected, $childBlock->getClassType());
    }
}
