<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion;

/**
 * @magentoAppArea adminhtml
 */
class ProductsTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\View\Block\AbstractBlock */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        $this->_block = $layout->createBlock('Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion\Products');
    }

    public function testPrepareLayout()
    {
        $searchBlock = $this->_block->getChildBlock('search_button');
        $this->assertInstanceOf('Magento\Backend\Block\Widget\Button', $searchBlock);
        $this->assertEquals('checkoutObj.searchProducts()', $searchBlock->getOnclick());
    }
}
