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

namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Options;

/**
 * @magentoAppArea adminhtml
 */
class OptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetOptionValuesCaching()
    {
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Options\Option');
        /** @var $productWithOptions \Magento\Catalog\Model\Product */
        $productWithOptions = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
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

            ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->setStatus(\Magento\Catalog\Model\Product\Status::STATUS_ENABLED);

        $product = clone $productWithOptions;
        /** @var $option \Magento\Catalog\Model\Product\Option */
        $option = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product\Option',
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
