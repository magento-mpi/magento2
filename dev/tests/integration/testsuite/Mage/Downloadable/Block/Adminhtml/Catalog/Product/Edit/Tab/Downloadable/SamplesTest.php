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

class Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_SamplesTest
    extends PHPUnit_Framework_TestCase
{
    public function testGetUploadButtonsHtml()
    {
        $block = new Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples;
        Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_LinksTest
            ::performUploadButtonTest($block);
    }

    public function testGetSampleData()
    {
        Mage::register('current_product', new Varien_Object(array('type_id' => 'simple')));
        $block = Mage::app()->getLayout()
            ->createBlock('Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples');
        $this->assertEmpty($block->getSampleData());
    }


    protected function tearDown()
    {
        Mage::unregister('current_product');
    }

    /**
     * Get Samples Title for simple and virtual products
     *
     * @magentoAppIsolation enabled
     * @dataProvider productTypesDataProvider
     *
     * @param string $productType
     */
    public function testGetSamplesTitle($productType)
    {
        Mage::register('current_product', new Varien_Object(array('type_id' => $productType, 'id' => '1')));
        $block = Mage::app()->getLayout()
            ->createBlock('Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples');
        $this->assertEquals('Samples', $block->getSamplesTitle());
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
     * Get Samples Title for downloadable product with existed Samples Title value
     *
     * @magentoAppIsolation enabled
     * @dataProvider samplesTitleDataProvider
     *
     * @param string $samplesTitle
     */
    public function testGetSamplesTitleExist($samplesTitle)
    {
        Mage::register('current_product',
            new Varien_Object(array('type_id' => 'downloadable', 'id' => '1', 'samples_title' => $samplesTitle)));
        $block = Mage::app()->getLayout()
            ->createBlock('Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples');
        $this->assertEquals($samplesTitle, $block->getSamplesTitle());
    }

    /**
     * Data Provider with Samples Title values
     *
     * @return array
     */
    public function samplesTitleDataProvider()
    {
        return array (
            array(null),
            array('Samples Test')
        );
    }
}
