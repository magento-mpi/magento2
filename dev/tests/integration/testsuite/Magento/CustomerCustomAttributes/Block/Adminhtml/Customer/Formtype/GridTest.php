<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype;

/**
 * @magentoAppArea adminhtml
 */
class GridTest extends \PHPUnit_Framework_TestCase
{
    public function testPrepareColumns()
    {
        /** @var \Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype\Grid $block */
        $block = \Mage::app()->getLayout()->createBlock(
            'Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype\Grid');
        $block->toHtml();
        foreach (array('code', 'label', 'store_id', 'theme', 'is_system') as $key) {
            $this->assertInstanceOf('Magento\Backend\Block\Widget\Grid\Column', $block->getColumn($key));
        }
        $this->assertNotEmpty($block->getColumn('theme')->getOptions());
    }
}
