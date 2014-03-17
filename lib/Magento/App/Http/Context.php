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
     * @var array
     */
    protected $default = array();

    /**
     * Data setter
     *
     * @param string $name
     * @param mixed $value
     * @param mixed $default
     * @return \Magento\App\Http\Context
     */
    public function setValue($name, $value, $default)
    {
        if ($default !== null) {
            $this->default[$name] = $default;
        }
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * Unset data from vary array
     *
     * @param string $name
     * @return null
     */
    public function unsValue($name)
    {
        unset($this->data[$name]);
        return;
    }

    /**
     * Data getter
     *
     * @param string $name
     * @return mixed
     */
    public function getValue($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : $this->default[$name];
    }

    /**
     * Set default values
     *
     * @param $name
     * @param $value
     * @return \Magento\App\Http\Context
     */
    public function setDefaultValue($name, $value)
    {
        $this->default[$name] = $value;
        return $this;
    }

    /**
     * Return all data
     *
     * @return array
     */
    public function getData()
    {
        $data = [];
        foreach ($this->data as $name => $value) {
            if ($value && $value != $this->default[$name]) {
                $data[$name] = $value;
            }
        }
        return $data;
    }
}
