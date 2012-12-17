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
            $dateFrom = date('m/d/y');
        }
        if ($dateTo === null) {
            $dateTo = date('m/d/y');
        }
        $this->fillField('filter_from', $dateFrom);
        $this->fillField('filter_to', $dateTo);
    }

    /**
     *  Method that goes through test data array and adds Verification Messages
     *
     * @param $data
     * @param $testChildDataName
     * @param string $exclude
     *
     * @return mixed
     */
    public function prepareData($data, $testChildDataName, $exclude = 'headers')
    {
        if (array_key_exists($exclude, $data[$testChildDataName])) {
            $headers = $data[$testChildDataName][$exclude];
            unset($data[$testChildDataName][$exclude]);

            return $headers;
        }
        foreach ($data[$testChildDataName] as $control => $type) {
            foreach ($type as $typeName => $name) {
                if (!$this->controlIsPresent($control, $typeName)) {
                    $this->addVerificationMessage("The $control $typeName is not present on page $testChildDataName");
                }
            }
        }
    }

    /**
     * Get Header names from grid and
     *
     * @param $data
     * @param string $fieldsetFlag
     *
     * @return array
     */
    public function getGridHeaders($data, $fieldsetFlag = 'tablename')
    {
        $tableNameValue = array_search($fieldsetFlag, $data['fieldset']);
        if ($tableNameValue) {
            $tableXpath = $this->_getControlXpath('fieldset', $tableNameValue);
            $actualHeadersName = $this->getTableHeadRowNames($tableXpath);
            return $actualHeadersName;

        } else {
            $this->fail("Should be at least one key in field section with value $fieldsetFlag");
        }
    }
}