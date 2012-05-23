<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Parameters helper class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     {license_link}
 */
class Mage_Selenium_Helper_Params
{
    /**
     * Parameters array
     * @var array
     */
    protected $_paramsArray = array();

    /**
     * Class constructor
     *
     * @param array $params Parameters array
     */
    public function __construct(array $params = null)
    {
        if (!empty($params)) {
            foreach ($params as $paramName => $paramValue) {
                $this->setParameter($paramName, $paramValue);
            }
        }
    }

    /**
     * Set Xpath parameter
     *
     * @param string $name Parameter name
     * @param string $value Parameter value (null to unset)
     *
     * @return \Mage_Selenium_Helper_Params
     */
    public function setParameter($name, $value)
    {
        $key = '%' . $name . '%';
        if ($value === null) {
            unset($this->_paramsArray[$key]);
        } else {
            $this->_paramsArray[$key] = $value;
        }
        return $this;
    }

    /**
     * Get Xpath parameter
     *
     * @param string $name Parameter name
     *
     * @return string|boolean Returns the parameter value or False
     */
    public function getParameter($name)
    {
        $key = '%' . $name . '%';
        return isset($this->_paramsArray[$key]) ? $this->_paramsArray[$key] : false;
    }

    /**
     * Populate string with parameter values
     *
     * @param string $source Source string
     *
     * @return string
     */
    public function replaceParameters($source)
    {
        if (empty($this->_paramsArray) || !is_string($source) || empty($source)) {
            return $source;
        }
        return str_replace(array_keys($this->_paramsArray), array_values($this->_paramsArray), $source);

    }

    /**
     * Populate string with Regexp for future matching
     *
     * @param string $source Source string
     * @param string $regexp Regular expression (by default = '([^\/]+?)')
     *
     * @return string
     */
    public function replaceParametersWithRegexp($source, $regexp = '([^\/]+?)')
    {
        if ($this->_paramsArray) {
            return str_replace(array_keys($this->_paramsArray), $regexp, $source);
        }
        return $source;
    }
}
