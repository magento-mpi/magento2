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
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_OptionTest extends PHPUnit_Framework_TestCase
{
    public function testGetOptionValuesCaching()
    {
        $block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
            ->createBlock('Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option');
        /** @var $productWithOptions Magento_Catalog_Model_Product */
        $productWithOptions = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product');
        $productWithOptions->setTypeId('simple')
            ->setId(1)
            ->setAttributeSetId(4)
            ->setWebsiteIds(array(1))
            ->setName('Simple Product With Custom Options')
            ->setSku('simple')
            ->setPrice(10)

            ->setMetaTitle('meta title')
            ->setMetaKeyword('meta keyword')
            ->setMetaDescription('meta description')

            ->setVisibility(Magento_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->setStatus(Magento_Catalog_Model_Product_Status::STATUS_ENABLED);

        $product = clone $productWithOptions;
        /** @var $option Magento_Catalog_Model_Product_Option */
        $option = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create(
            'Magento_Catalog_Model_Product_Option',
            array('data' => array('id' => 1, 'title' => 'some_title'))
        );
        $productWithOptions->addOption($option);

        $block->setProduct($productWithOptions);
        $this->assertNotEmpty($block->getOptionValues());

        $block->setProduct($product);
        $this->assertNotEmpty($block->getOptionValues());

        $block->setIgnoreCaching(true);
        $this->assertEmpty($block->getOptionValues());
    }
}
