<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_Customer
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Formtype_GridTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareColumns()
    {
        /** @var Enterprise_Customer_Block_Adminhtml_Customer_Formtype_Grid $block */
        $block = Mage::app()->getLayout()->createBlock('Enterprise_Customer_Block_Adminhtml_Customer_Formtype_Grid');
        // $block = new Enterprise_Customer_Block_Adminhtml_Customer_Formtype_Grid;
        $block->toHtml();
        foreach (array('code', 'label', 'store_id', 'theme', 'is_system') as $key) {
            $this->assertInstanceOf('Mage_Adminhtml_Block_Widget_Grid_Column', $block->getColumn($key));
        }
        $this->assertNotEmpty($block->getColumn('theme')->getOptions());
    }
}
