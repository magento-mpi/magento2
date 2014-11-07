<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\ProductLink;

class Link extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magento\Catalog\Api\Data\ProductLinkInterface
{
    /**
     * @var array
     */
    protected $_data;

    /**
     * Initialize internal storage
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->_data = $data;
    }

    /**
     * Retrieves a value from the data array if set, or null otherwise.
     *
     * @param string $key
     * @return mixed|null
     */
    protected function _get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    /**
     * Return Data Object data in array format.
     *
     * @return array
     */
    public function __toArray()
    {
        $data = $this->_data;
        $hasToArray = function ($model) {
            return is_object($model) && method_exists($model, '__toArray') && is_callable([$model, '__toArray']);
        };
        foreach ($data as $key => $value) {
            if ($hasToArray($value)) {
                $data[$key] = $value->__toArray();
            } elseif (is_array($value)) {
                foreach ($value as $nestedKey => $nestedValue) {
                    if ($hasToArray($nestedValue)) {
                        $value[$nestedKey] = $nestedValue->__toArray();
                    }
                }
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * Get product SKU
     *
     * @identifier
     * @return string
     */
    public function getProductSku()
    {
        return $this->_get(self::PRODUCT_SKU);
    }

    /**
     * Get link type
     *
     * @identifier
     * @return string
     */
    public function getLinkType()
    {
        return $this->_get(self::LINK_TYPE);
    }

    /**
     * Get linked product sku
     *
     * @identifier
     * @return string
     */
    public function getLinkedProductSku()
    {
        return $this->_get(self::LINKED_PRODUCT_SKU);
    }

    /**
     * Get linked product type (simple, virtual, etc)
     *
     * @return string
     */
    public function getLinkedProductType()
    {
        return $this->_get(self::LINKED_PRODUCT_TYPE);
    }

    /**
     * Get product position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }
}
