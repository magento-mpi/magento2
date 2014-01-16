<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Entity;

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
     * Return DTO data in array format.
     *
     * This only handles use cases of nested DTOs and array of DTOs
     *
     * @return \ArrayAccess
     */
    public function __toArray()
    {
        foreach ($this->_data as $key => $value) {
            if ($value instanceof AbstractDto) {
                $this->_data[$key] = $value->__toArray();
            } else if (is_array($value)) {
                foreach ($value as $vKey => $vValue) {
                    if ($vValue instanceof AbstractDto) {
                        $this->_data[$vKey] = $vValue->__toArray();
                    }
                }
            }
        }
        return $this->_data;
    }
}
