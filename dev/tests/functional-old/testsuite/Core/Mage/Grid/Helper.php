<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Grid
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Grid_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * By default fill fields with current date in default Magento format MM/DD/YY
     *
     * @param null $dateFrom
     * @param null $dateTo
     */
    public function fillDateFromTo($dateFrom = null, $dateTo = null)
    {
        if ($dateFrom === null) {
            $dateFrom = date("n/j/Y");
        } else {
            $dateFrom = date_format(new DateTime($dateFrom), "n/j/Y");
        }
        if ($dateTo === null) {
            $dateTo = date("n/j/Y");
        } else {
            $dateTo = date_format(new DateTime($dateTo), "n/j/Y");
        }
        $this->fillField('filter_from', $dateFrom);
        $this->fillField('filter_to', $dateTo);
    }

    /**
     *  Method that goes through test data array and adds verification messages
     *
     * @param array $data
     * @param string $exclude
     * @return mixed
     */
    public function prepareData(array $data, $exclude = 'headers')
    {
        if (array_key_exists($exclude, $data)) {
            unset($data[$exclude]);
        }
        foreach ($data as $control => $type) {
            foreach ($type as $typeName => $name) {
                if (!$this->controlIsPresent($control, $typeName)) {
                    $this->addVerificationMessage("The $control $typeName is not present on page");
                }
            }
        }
    }

    /**
     * Get header names from grid
     *
     * @param array $data
     * @param string $fieldsetFlag
     * @return array
     */
    public function getGridHeaders(array $data, $fieldsetFlag = 'tablename')
    {
        $tableNameValue = array_search($fieldsetFlag, $data['fieldset']);
        if (!$tableNameValue) {
            $this->fail("Should be at least one key in field section with value $fieldsetFlag");
        }
        return $this->getTableHeadRowNames($this->_getControlXpath('fieldset', $tableNameValue));
    }
}