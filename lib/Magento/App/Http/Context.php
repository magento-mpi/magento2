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
    const CUSTOMER_SEGMENT  = 'customer_segment';
    const CORE_STORE        = 'core_store';
    const CUSTOMER_GROUP    = 'customer_group';
    const CURRENT_CURRENCY  = 'current_currency';
    const CUSTOMER_AUTH     = 'customer_logged_in';

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
     * Unset data from vary array
     *
     * @param string $name
     */
    public function unsValue($name)
    {
        unset($this->data[$name]);
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
