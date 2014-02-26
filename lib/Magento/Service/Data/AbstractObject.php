<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Data;

/**
 * Class AbstractObject
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractObject
{
    const CUSTOM_ATTRIBUTES_KEY = 'custom_attributes';

    /**
     * @var array
     */
    protected $_data;

    /**
     * Initialize internal storage
     *
     * @param AbstractObjectBuilder $builder
     */
    public function __construct(AbstractObjectBuilder $builder)
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
        foreach ($data as $key => $value) {
            if (method_exists($value, '__toArray')) {
                $data[$key] = $value->__toArray();
            } else if (is_array($value)) {
                foreach ($value as $nestedArrayKey => $nestedArrayValue) {
                    if (method_exists($nestedArrayValue, '__toArray')) {
                        $data[$nestedArrayKey] = $nestedArrayValue->__toArray();
                    }
                }
            }
        }
        return $data;
    }
}
