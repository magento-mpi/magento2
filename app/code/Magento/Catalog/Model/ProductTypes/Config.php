<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\ProductTypes;

class Config extends \Magento\Config\Data implements \Magento\Catalog\Model\ProductTypes\ConfigInterface
{
    /**
     * @param \Magento\Catalog\Model\ProductTypes\Config\Reader $reader
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Catalog\Model\ProductTypes\Config\Reader $reader,
        \Magento\Config\CacheInterface $cache,
        $cacheId = 'product_types_config'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * Get configuration of product type by name
     *
     * @param string $name
     * @return array
     */
    public function getType($name)
    {
        return $this->get('types/' . $name, array());
    }

    /**
     * Get configuration of all registered product types
     *
     * @return array
     */
    public function getAll()
    {
        return $this->get('types');
    }

    /**
     * Check whether product type is set of products
     *
     * @param string $typeId
     * @return bool
     */
    public function isProductSet($typeId)
    {
        return 'true' == $this->get('types/' . $typeId . '/custom_attributes/is_product_set', false);
    }

    /**
     * Get composable types
     *
     * @return array
     */
    public function getComposableTypes()
    {
        return $this->get('composableTypes', array());
    }

    /**
     * Get available products
     *
     * @param string $customAttributeName
     * @param string $value
     * @return array
     */
    public function filter($customAttributeName, $value = 'true')
    {
        $availableProductTypes = array();
        foreach ($this->getAll() as $type) {
            if (!isset($type['custom_attributes'][$customAttributeName])
                || $type['custom_attributes'][$customAttributeName] == $value) {
                $availableProductTypes[] = $type['name'];
            }
        }
        return $availableProductTypes;
    }
}
