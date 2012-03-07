<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_DesignEditor
 */
class Mage_DesignEditor_Block_WrappingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Block_Wrapping
     */
    protected $_block;

    public function setUp()
    {
        $this->_block = new Mage_DesignEditor_Block_Wrapping;
    }

    public function testToHtml()
    {
        $block = new Mage_Core_Block_Template;
        $block->setNameInLayout('test.name');
        $html = '<div>Test Html</div>';
        $this->_block->setWrappedBlock($block)
            ->setWrappedHtml($html);

        $wrappedHtml = $this->_block->toHtml();

        $regexp = '/^\\s*<br([^>]+)>\\s*' . preg_quote($html, '/') . '\\s*<br([^>]+)>\\s*/';
        $matched = preg_match($regexp, $wrappedHtml, $matches);
        $this->assertNotEmpty($matched);

        $startingMarker = $matches[1];
        $endingMarker = $matches[2];
        $this->assertContains('marker_type="start"', $startingMarker);
        $this->assertContains('block_name="test.name"', $startingMarker);
        $this->assertContains('marker_type="end"', $endingMarker);
    }

    /**
     * @expectedException Mage_DesignEditor_Exception
     */
    public function testToHtmlExceptionOnNoBlock()
    {
        $this->_block->toHtml();
    }
}
