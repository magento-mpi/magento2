<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Adminhtml_Block_Report_Grid
 */
class Mage_Adminhtml_Block_Report_GridTest extends PHPUnit_Framework_TestCase
{
    public function testGetDateFormat()
    {
        $block = Mage::getObjectManager()->create('Mage_Adminhtml_Block_Report_Grid');
        $this->assertNotEmpty($block->getDateFormat());
    }
}
