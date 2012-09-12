<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Customer_Block_Widget_Dob.
 *
 * @group module:Mage_Customer
 */
class Mage_Customer_Block_Widget_DobTest extends PHPUnit_Framework_TestCase
{
    public function testGetDateFormat()
    {
        $block = new Mage_Customer_Block_Widget_Dob;
        $this->assertNotEmpty($block->getDateFormat());
    }
}
