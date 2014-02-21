<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Entity;

use Magento\Service\Entity\AbstractDtoBuilder;

/**
 * Class AbstractDto
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractDto
{
    /**
     * @var array
     */
    protected $_data;

    /**
     * Initialize internal storage
     *
     * @param AbstractDtoBuilder $builder
     */
    public function __construct(AbstractDtoBuilder $builder)
    {
        $this->_data = $builder->getData();
    }

    /**
     * Retrieve custom attributes values as an associative array.
     *
     * @return array
     */
    public function getCustomAttributes()
    {
        return isset($this->_data[AbstractDtoBuilder::CUSTOM_ATTRIBUTES_KEY])
            ? $this->_data[AbstractDtoBuilder::CUSTOM_ATTRIBUTES_KEY]
            : [];
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
     * Return DTO data in array format.
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
