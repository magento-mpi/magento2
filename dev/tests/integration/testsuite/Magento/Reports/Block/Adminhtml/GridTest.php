<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Reports_Block_Adminhtml_Grid
 * @magentoAppArea adminhtml
 */
class Magento_Reports_Block_Adminhtml_GridTest extends PHPUnit_Framework_TestCase
{
    public function testGetDateFormat()
    {
        /** @var $block Magento_Reports_Block_Adminhtml_Grid */
        $block = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Magento_Reports_Block_Adminhtml_Grid');
        $this->assertNotEmpty($block->getDateFormat());
    }
}
