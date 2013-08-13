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
 * Test class for Mage_Customer_Block_Widget_Dob
 */
class Mage_Customer_Block_Widget_DobTest extends PHPUnit_Framework_TestCase
{
    public function testGetDateFormat()
    {
        $block = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Mage_Customer_Block_Widget_Dob');
        $this->assertNotEmpty($block->getDateFormat());
    }
}
