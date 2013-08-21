<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     selenium
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
class Enterprise_Mage_Customer_Helper extends Core_Mage_Customer_Helper
{
    /**
     * Updating Customer Store Credit Balance
     *
     * @param array $storeCreditData Store credit Information
     * @param boolean $continue Press Save And Continue instead of Save
     *
     * @return void
     */
    public function updateStoreCreditBalance(array $storeCreditData, $continue = false)
    {
        $this->fillTab($storeCreditData, 'store_credit');
        if (!$continue) {
            $this->saveForm('save_customer');
        }
    }

    /**
     * Updating Customer Reward Points Balance
     *
     * @param array $rewardPointsData Store credit Information
     * @param boolean $continue Press Save And Continue instead of Save
     *
     * @return void
     */
    public function updateRewardPointsBalance(array $rewardPointsData, $continue = false)
    {
        $this->fillTab($rewardPointsData, 'reward_points');
        if (!$continue) {
            $this->saveForm('save_customer');
        }
    }

    /**
     * Get Current Customer Store Credit Balance
     *
     * @param string $webSiteName
     *
     * @return string
     */
    public function getStoreCreditBalance($webSiteName = '')
    {
        $this->openTab('store_credit');
        $fieldsetLocator = $this->_getControlXpath('fieldset', 'current_store_balance');
        $this->addParameter('tableXpath', $fieldsetLocator);
        if ($this->controlIsVisible('message', 'specific_table_no_records_found')) {
            return 'No records found.';
        }
        $this->addParameter('webSiteName', $webSiteName);
        $this->addParameter('index', $this->getColumnIdByName('Balance', $fieldsetLocator));
        return trim($this->getControlAttribute('field', 'store_credit_balance', 'text'));
    }

    /**
     * Get Current Customer Store Credit Balance
     *
     * @param string $webSiteName
     *
     * @return string
     */
    public function getRewardPointsBalance($webSiteName = '')
    {
        $this->openTab('reward_points');
        $fieldsetLocator = $this->_getControlXpath('fieldset', 'current_reward_balance');
        $this->addParameter('tableXpath', $fieldsetLocator);
        if ($this->controlIsVisible('message', 'specific_table_no_records_found')) {
            return 'No records found.';
        }
        $this->addParameter('webSiteName', $webSiteName);
        $this->addParameter('index', $this->getColumnIdByName('Balance', $fieldsetLocator));
        return trim($this->getControlAttribute('field', 'reward_points_balance', 'text'));
    }

    /*
    * Searches the specified row in Reward Points History.
    * Returns row number(s) if found, or null otherwise.
    *
    * @param array $data Data to look for
    *
    * @return string|array|null
    */
    public function searchRewardPointsHistoryRecord(array $data)
    {
        $rowNumbers = array();

        if (!$this->controlIsPresent('fieldset', 'reward_points_history')) {
            $this->clickControl('link', 'reward_points_history_link', false);
            $this->waitForAjax();
        }
        $totalCount = intval($this->getControlAttribute('pageelement', 'reward_points_history_rows_number', 'text'));
        $xpathTR = $this->formSearchXpath($data);
        if ($this->getElement($xpathTR)->displayed()) {
            for ($i = 1; $i <= $totalCount; $i++) {
                if ($this->getElement(str_replace('tr', 'tr[' . $i . ']', $xpathTR))->displayed()) {
                    $rowNumbers[] = $i;
                }
            }
            if (count($rowNumbers) == 1) {
                return $rowNumbers[0];
            } else {
                return $rowNumbers;
            }
        }
        return null;
    }
}