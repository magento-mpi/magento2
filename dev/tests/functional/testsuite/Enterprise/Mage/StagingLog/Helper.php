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
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_StagingLog_Helper extends Mage_Selenium_TestCase
{
    /**
     * <p>Open Log</p>
     *
     * @param array $searchData
     *
     * @return bool
     */
    public function openLog(array $searchData)
    {
        $this->searchAndOpen($searchData);
    }
}
