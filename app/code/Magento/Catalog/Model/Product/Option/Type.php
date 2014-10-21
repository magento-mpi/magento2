<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Option;

class Type implements \Magento\Catalog\Api\Data\ProductCustomOptionOptionTypeInterface
{
    /**
     * @var array
     */
    protected $_data;

    /**
     * Initialize internal storage
     *
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionOptionTypeInterfaceBuilder $builder
     */
    public function __construct(\Magento\Catalog\Api\Data\ProductCustomOptionOptionTypeInterfaceBuilder $builder)
    {
        $this->_data = $builder->getData();
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
     * Get option type label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_get(self::LABEL);
    }

    /**
     * Get option type code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_get(self::CODE);
    }

    /**
     * Get option type group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->_get(self::GROUP);
    }
}
