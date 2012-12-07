<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Simple class for pool of data.
 * Key-value data.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_StaticDataPool_Simple extends Mage_PHPUnit_StaticDataPool_Abstract
{
    /**
     * Key-value data array
     *
     * @var array array(key => value)
     */
    protected $_data = array();

    /**
     * Value getter by key.
     *
     * @param string $key
     * @return mixed
     */
    public function getData($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : false;
    }

    /**
     * Value setter for key
     *
     * @param string $key
     * @param mixed $value
     * @return Mage_PHPUnit_StaticDataPool_Simple
     */
    public function setData($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }
}
