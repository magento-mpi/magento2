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

/**
 * @magentoAppArea adminhtml
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Gallery;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUploader()
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout');
        /** @var $block \Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Gallery\Content */
        $block = $layout->createBlock('Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Gallery\Content', 'block');

        $this->assertInstanceOf('Magento\Adminhtml\Block\Media\Uploader', $block->getUploader());
    }
}
