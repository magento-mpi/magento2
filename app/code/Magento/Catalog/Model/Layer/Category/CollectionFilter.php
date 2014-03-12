<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer\Category;


use Magento\Catalog\Model\Layer\CollectionFilterInterface;

class CollectionFilter implements CollectionFilterInterface
{
    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $catalogProductVisibility;

    /**
     * Catalog config
     *
     * @var \Magento\Catalog\Model\Config
     */
    protected $catalogConfig;

    public function __construct(
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Model\Config $catalogConfig
    ) {
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->catalogConfig = $catalogConfig;
    }

    /**
     * @param \Magento\Catalog\Model\Resource\Product\Collection $collection
     * @param $category
     */
    public function filter(
        \Magento\Catalog\Model\Resource\Product\Collection $collection,
        \Magento\Catalog\Model\Category $category
    ) {
        $collection
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite($category->getId())
            ->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
    }
} 
