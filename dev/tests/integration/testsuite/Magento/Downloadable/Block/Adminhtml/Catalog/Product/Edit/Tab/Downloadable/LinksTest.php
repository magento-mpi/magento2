<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable;

class LinksTest
    extends \PHPUnit_Framework_TestCase
{
    public function testGetUploadButtonsHtml()
    {
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Links');
        self::performUploadButtonTest($block);
    }

    /**
     * Reuse code for testing getUploadButtonHtml()
     *
     * @param \Magento\Core\Block\AbstractBlock $block
     */
    public static function performUploadButtonTest(\Magento\Core\Block\AbstractBlock $block)
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Layout');
        $layout->addBlock($block, 'links');
        $expected = uniqid();
        $text = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Core\Block\Text', '', array('data' => array('text' => $expected)));
        $block->unsetChild('upload_button');
        $layout->addBlock($text, 'upload_button', 'links');
        self::assertEquals($expected, $block->getUploadButtonHtml());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetLinkData()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')
            ->register('product', new \Magento\Object(array('type_id' => 'simple')));
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Links');
        $this->assertEmpty($block->getLinkData());
    }

    /**
     * Get Links Title for simple/virtual/downloadable product
     *
     * @magentoConfigFixture current_store catalog/downloadable/links_title Links Title Test
     * @magentoAppIsolation enabled
     * @dataProvider productLinksTitleDataProvider
     *
     * @param string $productType
     * @param string $linksTitle
     * @param string $expectedResult
     */
    public function testGetLinksTitle($productType, $linksTitle, $expectedResult)
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('product', new \Magento\Object(array(
            'type_id' => $productType,
            'id' => '1',
            'links_title' => $linksTitle
        )));
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Links');
        $this->assertEquals($expectedResult, $block->getLinksTitle());
    }

    /**
     * Data Provider with product types
     *
     * @return array
     */
    public function productLinksTitleDataProvider()
    {
        return array (
            array('simple', null, 'Links Title Test'),
            array('simple', 'Links Title', 'Links Title Test'),
            array('virtual', null, 'Links Title Test'),
            array('virtual', 'Links Title', 'Links Title Test'),
            array('downloadable', null, null),
            array('downloadable', 'Links Title', 'Links Title')
        );
    }
}
