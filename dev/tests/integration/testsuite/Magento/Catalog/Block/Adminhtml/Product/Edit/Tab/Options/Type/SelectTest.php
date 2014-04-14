<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Type;

/**
 * @magentoAppArea adminhtml
 */
class SelectTest extends \PHPUnit_Framework_TestCase
{
    public function testToHtmlFormId()
    {
        /** @var $layout \Magento\View\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        /** @var $block \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Type\Select */
        $block = $layout->createBlock(
            'Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Type\Select',
            'select'
        );
        $html = $block->getPriceTypeSelectHtml();
        $this->assertContains('select_${select_id}', $html);
        $this->assertContains('[${select_id}]', $html);
    }
}
