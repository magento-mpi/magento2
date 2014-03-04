<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Entity;

use Magento\Exception\InputException;

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
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct($data)
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
            } elseif (is_array($value)) {
                foreach ($value as $nestedArrayKey => $nestedArrayValue) {
                    if (method_exists($nestedArrayValue, '__toArray')) {
                        $value[$nestedArrayKey] = $nestedArrayValue->__toArray();
                    }
                }
            }
        }
        return $data;
    }
}
