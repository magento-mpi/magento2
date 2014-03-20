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
namespace Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery;

/**
 * @magentoAppArea adminhtml
 */
class ContentTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUploader()
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        /** @var $block \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content */
        $block = $layout->createBlock('Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content', 'block');

        $this->assertInstanceOf('Magento\Backend\Block\Media\Uploader', $block->getUploader());
    }
}
