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
 * Test for Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Grid
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Block_Urlrewrite_Cms_Page_GridTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test prepare grid
     */
    public function testPrepareGrid()
    {
        /** @var Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Grid $gridBlock */
        $gridBlock = Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Grid');
        $gridBlock->toHtml();

        foreach (array('title', 'identifier', 'is_active') as $key) {
            $this->assertInstanceOf('Mage_Backend_Block_Widget_Grid_Column', $gridBlock->getColumn($key),
                'Column with key "' . $key . '" is invalid');
        }

        $this->assertStringStartsWith('http://localhost/index.php', $gridBlock->getGridUrl(),
            'Grid URL is invalid');

        $row = new Varien_Object(array('id' => 1));
        $this->assertStringStartsWith('http://localhost/index.php/cms_page/1', $gridBlock->getRowUrl($row),
            'Grid row URL is invalid');

        $this->assertEmpty(0, $gridBlock->getMassactionBlock()->getItems(), 'Grid should not have mass action items');
        $this->assertTrue($gridBlock->getUseAjax(), '"use_ajax" value of grid is incorrect');
    }

    /**
     * Test prepare grid when there is more than one store
     *
     * @magentoDataFixture Mage/Core/_files/store.php
     */
    public function testPrepareGridForMultipleStores()
    {
        /** @var Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Grid $gridBlock */
        $gridBlock = Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Grid');
        $gridBlock->toHtml();
        $this->assertInstanceOf('Mage_Backend_Block_Widget_Grid_Column', $gridBlock->getColumn('store_id'),
            'When there is more than one store column with key "store_id" should be present');
    }
}
