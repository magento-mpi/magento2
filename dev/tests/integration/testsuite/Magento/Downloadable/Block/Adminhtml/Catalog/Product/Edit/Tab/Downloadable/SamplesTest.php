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

class Magento_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_SamplesTest
    extends PHPUnit_Framework_TestCase
{
    public function testGetUploadButtonsHtml()
    {
        $block = Mage::app()->getLayout()->createBlock(
            '\Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Samples'
        );
        Magento_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_LinksTest
            ::performUploadButtonTest($block);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetSampleData()
    {
        Mage::register('current_product', new \Magento\Object(array('type_id' => 'simple')));
        $block = Mage::app()->getLayout()
            ->createBlock('\Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Samples');
        $this->assertEmpty($block->getSampleData());
    }

    /**
     * Get Samples Title for simple/virtual/downloadable product
     *
     * @magentoConfigFixture current_store catalog/downloadable/samples_title Samples Title Test
     * @magentoAppIsolation enabled
     * @dataProvider productSamplesTitleDataProvider
     *
     * @param string $productType
     * @param string $samplesTitle
     * @param string $expectedResult
     */
    public function testGetSamplesTitle($productType, $samplesTitle, $expectedResult)
    {
        Mage::register('current_product', new \Magento\Object(array(
            'type_id' => $productType,
            'id' => '1',
            'samples_title' => $samplesTitle
        )));
        $block = Mage::app()->getLayout()
            ->createBlock('\Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Samples');
        $this->assertEquals($expectedResult, $block->getSamplesTitle());
    }

    /**
     * Data Provider with product types
     *
     * @return array
     */
    public function productSamplesTitleDataProvider()
    {
        return array (
            array('simple', null, 'Samples Title Test'),
            array('simple', 'Samples Title', 'Samples Title Test'),
            array('virtual', null, 'Samples Title Test'),
            array('virtual', 'Samples Title', 'Samples Title Test'),
            array('downloadable', null, null),
            array('downloadable', 'Samples Title', 'Samples Title')
        );
    }
}
