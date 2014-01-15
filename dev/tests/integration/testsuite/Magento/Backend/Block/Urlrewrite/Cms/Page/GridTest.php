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

namespace Magento\Backend\Block\Urlrewrite\Cms\Page;

/**
 * Test for \Magento\Backend\Block\Urlrewrite\Cms\Page\Grid
 * @magentoAppArea adminhtml
 */
class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test prepare grid
     */
    public function testPrepareGrid()
    {
        /** @var \Magento\Backend\Block\Urlrewrite\Cms\Page\Grid $gridBlock */
        $gridBlock = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Backend\Block\Urlrewrite\Cms\Page\Grid');
        $gridBlock->toHtml();

        foreach (array('title', 'identifier', 'is_active') as $key) {
            $this->assertInstanceOf('Magento\Backend\Block\Widget\Grid\Column', $gridBlock->getColumn($key),
                'Column with key "' . $key . '" is invalid');
        }

        $this->assertStringStartsWith('http://localhost/index.php', $gridBlock->getGridUrl(),
            'Grid URL is invalid');

        $row = new \Magento\Object(array('id' => 1));
        $this->assertStringStartsWith(
            'http://localhost/index.php/backend/admin/index/edit/cms_page/1', $gridBlock->getRowUrl($row),
            'Grid row URL is invalid');

        $this->assertEmpty(0, $gridBlock->getMassactionBlock()->getItems(), 'Grid should not have mass action items');
        $this->assertTrue($gridBlock->getUseAjax(), '"use_ajax" value of grid is incorrect');
    }

    /**
     * Test prepare grid when there is more than one store
     *
     * @magentoDataFixture Magento/Core/_files/store.php
     */
    public function testPrepareGridForMultipleStores()
    {
        /** @var \Magento\Backend\Block\Urlrewrite\Cms\Page\Grid $gridBlock */
        $gridBlock = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Backend\Block\Urlrewrite\Cms\Page\Grid');
        $gridBlock->toHtml();
        $this->assertInstanceOf('Magento\Backend\Block\Widget\Grid\Column', $gridBlock->getColumn('store_id'),
            'When there is more than one store column with key "store_id" should be present');
    }
}
