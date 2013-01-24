<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Block_Page_Html_Head_VdeTest extends PHPUnit_Framework_TestCase
{
    public function testGetTemplate()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);
        /** @var $block Mage_DesignEditor_Block_Page_Html_Head_Vde */
        $block = $helper->getBlock('Mage_DesignEditor_Block_Page_Html_Head_Vde');

        $this->assertNull($block->getTemplate());
    }
}
