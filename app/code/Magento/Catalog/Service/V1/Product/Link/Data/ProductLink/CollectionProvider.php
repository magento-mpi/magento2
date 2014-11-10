<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data\ProductLink;

use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\ProductEntity\ConverterPool;

class CollectionProvider
{
    /**
     * @var CollectionProviderInterface[]
     */
    protected $providers;

    /**
     * @var ConverterPool
     */
    protected $converterPool;

    /**
     * @param ConverterPool $converterPool
     * @param CollectionProviderInterface[] $providers
     */
    public function __construct(ConverterPool $converterPool, array $providers = array())
    {
        $this->converterPool = $converterPool;
        $this->providers = $providers;
    }

    /**
     * Get product collection by link type
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $type
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCollection(\Magento\Catalog\Model\Product $product, $type)
    {
        if (!isset($this->providers[$type])) {
            throw new NoSuchEntityException('Collection provider is not registered');
        }

        $products = $this->providers[$type]->getLinkedProducts($product);
        $converter = $this->converterPool->getConverter($type);
        $output = [];
        foreach ($products as $item) {
            $output[$item->getId()] = $converter->convert($item);
        }
        return $output;
    }
}
