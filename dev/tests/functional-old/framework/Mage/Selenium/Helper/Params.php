<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Parameters helper class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Helper_Params
{
    /**
     * Parameters array
     * @var array
     */
    protected $_paramsArray = array();

    /**
     * @param array|null $params
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
     * Set a parameter
     *
     * @param string $name Parameter name
     * @param string $value Parameter value (null to unset)
     *
     * @throws OutOfRangeException
     * @return Mage_Selenium_Helper_Params
     */
    public function setParameter($name, $value)
    {
        if (is_array($value)) {
            throw new OutOfRangeException(
                'Can not add parameter ' . $name . ' because value type is array' . print_r($value, true));
        }
        $key = '%' . $name . '%';
        if ($value === null) {
            unset($this->_paramsArray[$key]);
        } else {
            $this->_paramsArray[$key] = $value;
        }
        return $this;
    }

    /**
     * Get parameter value
     *
     * @param string $name Parameter name
     *
     * @return string
     * @throws PHPUnit_Framework_Exception
     */
    public function getParameter($name)
    {
        $key = '%' . $name . '%';
        if (!array_key_exists($key, $this->_paramsArray)) {
            throw new PHPUnit_Framework_Exception('Parameter "' . $name . '" is not specified');
        }

        return $this->_paramsArray[$key];
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
        if (!empty($this->_paramsArray)) {
            $replaceKeys = array_keys($this->_paramsArray);
            $replaceKeys = array_map('preg_quote', $replaceKeys);
            return str_replace($replaceKeys, $regexp, $source);
        }
        return $source;
    }
}
