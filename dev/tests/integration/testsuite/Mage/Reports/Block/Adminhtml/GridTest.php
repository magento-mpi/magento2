<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Reports
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Reports_Block_Adminhtml_Grid
 */
class Mage_Reports_Block_Adminhtml_GridTest extends Mage_Backend_Area_TestCase
{
    public function testGetDateFormat()
    {
        /** @var $block Mage_Reports_Block_Adminhtml_Grid */
        $block = Mage::getObjectManager()->create('Mage_Reports_Block_Adminhtml_Grid');
        $this->assertNotEmpty($block->getDateFormat());
    }
}
