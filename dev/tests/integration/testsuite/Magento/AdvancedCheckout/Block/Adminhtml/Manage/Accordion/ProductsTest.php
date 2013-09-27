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

/**
 * @magentoAppArea adminhtml
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion;

class ProductsTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Core\Block\AbstractBlock */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout');
        $this->_block = $layout->createBlock('Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion\Products');
    }

    public function testPrepareLayout()
    {
        $searchBlock = $this->_block->getChildBlock('search_button');
        $this->assertInstanceOf('Magento\Backend\Block\Widget\Button', $searchBlock);
        $this->assertEquals('checkoutObj.searchProducts()', $searchBlock->getOnclick());
    }
}
