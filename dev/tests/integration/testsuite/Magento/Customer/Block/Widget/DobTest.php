<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Customer_Block_Widget_Dob
 */
class Magento_Customer_Block_Widget_DobTest extends PHPUnit_Framework_TestCase
{
    public function testGetDateFormat()
    {
        $block = Mage::getObjectManager()->create('Magento_Customer_Block_Widget_Dob');
        $this->assertNotEmpty($block->getDateFormat());
    }
}
