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

class Magento_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_LinksTest
    extends PHPUnit_Framework_TestCase
{
    public function testGetUploadButtonsHtml()
    {
        $block = Mage::app()->getLayout()->createBlock(
            'Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Links'
        );
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
        $layout = Mage::getModel('Magento\Core\Model\Layout');
        $layout->addBlock($block, 'links');
        $expected = uniqid();
        $text = Mage::app()->getLayout()->createBlock('Magento\Core\Block\Text', '',
            array('data' => array('text' => $expected))
        );
        $block->unsetChild('upload_button');
        $layout->addBlock($text, 'upload_button', 'links');
        self::assertEquals($expected, $block->getUploadButtonHtml());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetLinkData()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')
            ->register('product', new \Magento\Object(array('type_id' => 'simple')));
        $block = Mage::app()->getLayout()
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
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('product', new \Magento\Object(array(
            'type_id' => $productType,
            'id' => '1',
            'links_title' => $linksTitle
        )));
        $block = Mage::app()->getLayout()
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
