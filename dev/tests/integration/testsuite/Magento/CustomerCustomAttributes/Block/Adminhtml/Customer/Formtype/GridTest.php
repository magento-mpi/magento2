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

/**
 * @magentoAppArea adminhtml
 */
class Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Formtype_GridTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareColumns()
    {
        /** @var Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Formtype_Grid $block */
        $block = Mage::app()->getLayout()->createBlock(
            'Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Formtype_Grid');
        $block->toHtml();
        foreach (array('code', 'label', 'store_id', 'theme', 'is_system') as $key) {
            $this->assertInstanceOf('Magento_Backend_Block_Widget_Grid_Column', $block->getColumn($key));
        }
        $this->assertNotEmpty($block->getColumn('theme')->getOptions());
    }
}
