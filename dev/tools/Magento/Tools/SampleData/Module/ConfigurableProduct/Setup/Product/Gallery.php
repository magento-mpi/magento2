<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\SampleData\Module\ConfigurableProduct\Setup\Product;

/**
 * Class Gallery
 */
class Gallery extends \Magento\Tools\SampleData\Module\Catalog\Setup\Product\Gallery
{
    /**
     * {@inheritdoc}
     */
    protected $fixtures = [
        'ConfigurableProduct/images_men_bottoms.csv',
        'ConfigurableProduct/images_men_tops.csv',
        'ConfigurableProduct/images_women_bottoms.csv',
        'ConfigurableProduct/images_women_tops.csv',
        'ConfigurableProduct/images_gear_fitness_equipment.csv',
    ];

    /**
     * {@inheritdoc}
     */
    public function install($product)
    {
        parent::install($product);
        foreach ($product->getAssociatedProductIds() as $id) {
            $product = $this->productFactory->create()->load($id);
            parent::install($product);
        }
    }
}
