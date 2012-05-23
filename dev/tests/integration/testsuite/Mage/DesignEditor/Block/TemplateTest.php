<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Block_TemplateTest extends PHPUnit_Framework_TestCase
{
    public function testIsHighlightingDisabled()
    {
        $block = new Mage_DesignEditor_Block_Template;
        $this->assertFalse($block->isHighlightingDisabled());
        Mage::getSingleton('Mage_Core_Model_Cookie')->set(Mage_DesignEditor_Model_Session::COOKIE_HIGHLIGHTING, 'off');
        $this->assertTrue($block->isHighlightingDisabled());
        Mage::getSingleton('Mage_Core_Model_Cookie')->set(Mage_DesignEditor_Model_Session::COOKIE_HIGHLIGHTING, 'on');
        $this->assertFalse($block->isHighlightingDisabled());
    }
}
