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

class Mage_Adminhtml_Block_Tax_ClassTest extends PHPUnit_Framework_TestCase
{
    public function testSetClassType()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        $block = $layout->createBlock('Mage_Adminhtml_Block_Tax_Class', 'block');
        $childBlock = $block->getChildBlock('grid');
        $expected = Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT;
        $this->assertNull($childBlock->getClassType());
        $block->setClassType($expected);
        $this->assertEquals($expected, $childBlock->getClassType());
    }
}
