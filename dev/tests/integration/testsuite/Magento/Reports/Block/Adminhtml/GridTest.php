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

namespace Magento\Reports\Block\Adminhtml;

/**
 * Test class for \Magento\Reports\Block\Adminhtml\Grid
 * @magentoAppArea adminhtml
 */
class GridTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDateFormat()
    {
        /** @var $block \Magento\Reports\Block\Adminhtml\Grid */
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Reports\Block\Adminhtml\Grid');
        $this->assertNotEmpty($block->getDateFormat());
    }
}
