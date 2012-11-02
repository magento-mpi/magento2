<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_LinksTest
    extends PHPUnit_Framework_TestCase
{
    public function testGetUploadButtonsHtml()
    {
        $block = new Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links;
        self::performUploadButtonTest($block);
    }

    /**
     * Reuse code for testing getUploadButtonHtml()
     *
     * @param Mage_Core_Block_Abstract $block
     */
    public static function performUploadButtonTest(Mage_Core_Block_Abstract $block)
    {
        $layout = new Mage_Core_Model_Layout;
        $layout->addBlock($block, 'links');
        $expected = uniqid();
        $text = new Mage_Core_Block_Text(array('text' => $expected));
        $block->unsetChild('upload_button');
        $layout->addBlock($text, 'upload_button', 'links');
        self::assertEquals($expected, $block->getUploadButtonHtml());
    }

    public function testGetLinkData()
    {
        Mage::register('product', new Varien_Object(array('type_id' => 'simple')));
        $block = Mage::app()->getLayout()
            ->createBlock('Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links');
        $this->assertEmpty($block->getLinkData());
    }

    protected function tearDown()
    {
        Mage::unregister('product');
    }

    /**
     * Get Links Title for simple and virtual products
     *
     * @magentoAppIsolation enabled
     * @dataProvider productTypesDataProvider
     *
     * @param string $productType
     */
    public function testGetLinksTitle($productType)
    {
        Mage::register('product', new Varien_Object(array('type_id' => $productType, 'id' => '1')));
        $block = Mage::app()->getLayout()
            ->createBlock('Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links');
        $this->assertEquals('Links', $block->getLinksTitle());
    }

    /**
     * Data Provider with product types
     *
     * @return array
     */
    public function productTypesDataProvider()
    {
        return array (
            array('simple'),
            array('virtual'),
        );
    }

    /**
     * Get Links Title for downloadable product with existed Links Title value
     *
     * @magentoAppIsolation enabled
     * @dataProvider linksTitleDataProvider
     *
     * @param string $linksTitle
     */
    public function testGetLinksTitleExist($linksTitle)
    {
        Mage::register('product',
            new Varien_Object(array('type_id' => 'downloadable', 'id' => '1', 'links_title' => $linksTitle)));
        $block = Mage::app()->getLayout()
            ->createBlock('Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links');
        $this->assertEquals($linksTitle, $block->getLinksTitle());
    }

    /**
     * Data Provider with Links Title values
     *
     * @return array
     */
    public function linksTitleDataProvider()
    {
        return array (
            array(null),
            array('Links Test')
        );
    }
}
