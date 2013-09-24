<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block\Urlrewrite\Catalog\Product;

/**
 * Test for \Magento\Adminhtml\Block\Urlrewrite\Catalog\Product\Grid
 * @magentoAppArea adminhtml
 */
class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test prepare grid
     */
    public function testPrepareGrid()
    {
        /** @var $gridBlock \Magento\Adminhtml\Block\Urlrewrite\Catalog\Product\Grid */
        $gridBlock = \Mage::app()->getLayout()->createBlock('Magento\Adminhtml\Block\Urlrewrite\Catalog\Product\Grid');
        $gridBlock->toHtml();

        foreach (array('entity_id', 'name', 'sku', 'status') as $key) {
            $this->assertInstanceOf('Magento\Backend\Block\Widget\Grid\Column', $gridBlock->getColumn($key),
                'Column with key "' . $key . '" is invalid');
        }

        $this->assertStringStartsWith('http://localhost/index.php', $gridBlock->getGridUrl(),
            'Grid URL is invalid');

        $row = new \Magento\Object(array('id' => 1));
        $this->assertStringStartsWith('http://localhost/index.php/product/1', $gridBlock->getRowUrl($row),
            'Grid row URL is invalid');
        $this->assertStringEndsWith('/category', $gridBlock->getRowUrl($row), 'Grid row URL is invalid');

        $this->assertEmpty(0, $gridBlock->getMassactionBlock()->getItems(), 'Grid should not have mass action items');
        $this->assertTrue($gridBlock->getUseAjax(), '"use_ajax" value of grid is incorrect');
    }
}
