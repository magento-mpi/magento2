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

class SamplesTest
    extends \PHPUnit_Framework_TestCase
{
    public function testGetUploadButtonsHtml()
    {
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Samples');
        \Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\LinksTest
            ::performUploadButtonTest($block);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetSampleData()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')
            ->register('current_product', new \Magento\Object(array('type_id' => 'simple')));
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Samples');
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
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_product', new \Magento\Object(array(
            'type_id' => $productType,
            'id' => '1',
            'samples_title' => $samplesTitle
        )));
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Samples');
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
