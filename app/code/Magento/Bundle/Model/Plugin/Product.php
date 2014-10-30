<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model\Plugin;

use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Model\Product as CatalogProduct;

class Product
{

    /**
     * @var Type
     */
    private $type;

    /**
     * @param Type $type
     */
    public function __construct(Type $type)
    {
        $this->type = $type;
    }

    /**
     *
     * @param CatalogProduct $product
     * @param callable $proceed
     * @return string[]
     */
    public function aroundGetIdentities(
        CatalogProduct $product,
        \Closure $proceed
    ) {
        $identities = $proceed();
        foreach ($this->type->getParentIdsByChild($product->getId()) as $parentId) {
            $identities[] = CatalogProduct::CACHE_TAG . '_' . $parentId;
        }
        return $identities;
    }
}
