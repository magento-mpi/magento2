<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Csv
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Csv Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @method Community2_Mage_Csv_Helper helper(string $className)
 */
class Enterprise_Mage_Csv_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Convert CSV string to associative array
     *
     * @param string $input Input csv string to be converted to array
     * @param string $delimiter Delimiter
     *
     * @return array
     */
    public function csvToArray($input, $delimiter = ',')
    {
        return $this->helper('Community2/Mage/Csv/Helper')->csvToArray($input, $delimiter);
    }

    /**
     * Convert associative array to CSV string
     *
     * @param array $input Input associative array to be converted to string
     * @param string $delimiter Delimiter
     *
     * @return string
     */
    public function arrayToCsv(array $input, $delimiter = ',')
    {
        return $this->helper('Community2/Mage/Csv/Helper')->arrayToCsv($input, $delimiter);
    }
}