<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Http;

/**
 * Context data for requests
 */
class Context
{
    /**
     * Data storage
     *
     * @var array
     */
    protected $data = array();

    /**
     * Data setter
     *
     * @param string $name
     * @param mixed $value
     */
    public function setValue($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Data getter
     *
     * @param string $name
     * @return mixed
     */
    public function getValue($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Return all data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
