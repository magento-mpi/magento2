<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_UnitPrice_Helper_FakeData extends Saas_UnitPrice_Helper_Data
{
    private $_config = array();

    /**
     * Delegate method to protected parent method to make unit testing possible
     *
     * @param  Magento_Catalog_Model_Product $product
     * @return UnitPrice_Helper_Data
     */
    public function _loadDefaultUnitPriceValues($product)
    {
        return parent::_loadDefaultUnitPriceValues($product);
    }

    /**
     * Set fake config value
     * Used to avoid usage of magento configuration in purposes of unit testing
     *
     * @param string $key
     * @param mixed $value
     * @return Saas_UnitPrice_Helper_FakeData
     */
    public function setConfig($key, $value)
    {
        $this->_config[$key] = $value;
        return $this;
    }

    /**
     * Get fake config value
     * Used to avoid usage of magento configuration in purposes of unit testing
     *
     * @return mixed
     */
    public function getConfig($key)
    {
        return $this->_config[$key];
    }
}

