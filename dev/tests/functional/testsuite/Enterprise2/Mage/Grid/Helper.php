<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Newsletter
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     Mage_Grid
 * @subpackage  functional_tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise2_Mage_Grid_Helper extends Mage_Selenium_TestCase
{
    /**
     * @param null $dateFrom
     * @param null $dateTo
     *
     * By default fill fields with current date in default Magento format MM/DD/YY
     */
    public function refreshReport($dateFrom = null, $dateTo = null)
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
}