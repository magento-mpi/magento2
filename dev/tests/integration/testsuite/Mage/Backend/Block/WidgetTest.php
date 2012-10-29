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
 * Test class for Mage_Backend_Block_Widget
 */
class Mage_Backend_Block_WidgetTest extends PHPUnit_Framework_TestCase
{
    public function testGetSuffixId()
    {
        $block = Mage::getObjectManager()->create('Mage_Backend_Block_Widget');
        $this->assertStringEndsNotWith('_test', $block->getSuffixId('suffix'));
        $this->assertStringEndsWith('_test', $block->getSuffixId('test'));
    }
}
