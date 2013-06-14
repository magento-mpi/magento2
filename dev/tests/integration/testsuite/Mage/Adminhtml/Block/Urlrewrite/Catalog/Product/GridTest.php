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
 * Test for Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Grid
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_GridTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test prepare grid
     */
    public function testPrepareGrid()
    {
        /** @var $gridBlock Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Grid */
        $gridBlock = Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Grid');
        $gridBlock->toHtml();

        foreach (array('entity_id', 'name', 'sku', 'status') as $key) {
            $this->assertInstanceOf('Mage_Backend_Block_Widget_Grid_Column', $gridBlock->getColumn($key),
                'Column with key "' . $key . '" is invalid');
        }

        $this->assertStringStartsWith('http://localhost/index.php', $gridBlock->getGridUrl(),
            'Grid URL is invalid');

        $row = new Varien_Object(array('id' => 1));
        $this->assertStringStartsWith('http://localhost/index.php/product/1', $gridBlock->getRowUrl($row),
            'Grid row URL is invalid');
        $this->assertStringEndsWith('/category', $gridBlock->getRowUrl($row), 'Grid row URL is invalid');

        $this->assertEmpty(0, $gridBlock->getMassactionBlock()->getItems(), 'Grid should not have mass action items');
        $this->assertTrue($gridBlock->getUseAjax(), '"use_ajax" value of grid is incorrect');
    }
}
