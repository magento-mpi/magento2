<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data\LinkedProductEntity;

class CollectionProvider
{
    /**
     * @var CollectionProviderInterface[]
     */
    protected $providers;

    /**
     * @param array $providers
     */
    public function __construct(array $providers = array())
    {
        foreach ($providers as $providerData) {
            $this->providers[$providerData['code']] = $providerData['provider'];
        }
    }

    /**
     * Get product collection by link type
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $type
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getCollection(\Magento\Catalog\Model\Product $product, $type)
    {
        if (!isset($this->providers[$type])) {
            throw new \InvalidArgumentException('Collection provider for type ' . $type . ' is not registered');
        }
        return $this->providers[$type]->getLinkedProducts($product);
    }
}
