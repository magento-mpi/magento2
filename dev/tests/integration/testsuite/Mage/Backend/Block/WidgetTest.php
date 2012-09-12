<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Backend_Block_Widget.
 *
 * @group module:Mage_Backend
 */
class Mage_Backend_Block_WidgetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Block_Widget
     */
    protected  $_block;

    public function setUp()
    {
        $this->_block = new Mage_Backend_Block_Widget;
    }

    public function testGetSuffixId()
    {
        $suffix = 'test';
        $this->assertStringEndsNotWith('_' . $suffix, $this->_block->getSuffixId('suffix'));
        $this->assertStringEndsWith('_' . $suffix, $this->_block->getSuffixId($suffix));
    }
}
