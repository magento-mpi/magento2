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
        return isset($this->_data[$key]) ? $this->_data[$key]: null;
    }

    /**
     * Return DTO data in array format.
     *
     * @return array
     */
    public function __toArray()
    {
        return $this->_data;
    }
}
