<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Service\V1\Product\Link\Data\ProductLinkEntity\CollectionProvider;

use \Magento\GroupedProduct\Service\V1\Product\Link\Data\ProductLinkEntity\Converter;

class Grouped implements \Magento\Catalog\Service\V1\Product\Link\Data\ProductLinkEntity\CollectionProviderInterface
{
    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @param Converter $converter
     */
    public function __construct(Converter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkedProducts(\Magento\Catalog\Model\Product $product)
    {
        return $this->converter->convert($product->getTypeInstance()->getAssociatedProducts($product));
    }
}
