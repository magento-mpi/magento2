<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CustomerGroups
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
class Core_Mage_CustomerGroups_Helper extends Mage_Selenium_TestCase
{
    /**
     * Create new Customer Group
     *
     * @param array|string $customerGroupData
     */
    public function createCustomerGroup($customerGroupData)
    {
        if (is_string($customerGroupData)) {
            $elements = explode('/', $customerGroupData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $customerGroupData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $this->clickButton('add_new_customer_group');
        $this->fillForm($customerGroupData);
        $this->saveForm('save_customer_group');
    }

    /**
     * Open Customer Group
     *
     * @param array|string $searchData
     */
    public function openCustomerGroup($searchData)
    {
        if (is_string($searchData)) {
            $elements = explode('/', $searchData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $searchData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $xpathTR = $this->search($searchData, 'customer_group_grid');
        $this->assertNotNull($xpathTR, 'Customer Group is not found');
        $cellId = $this->getColumnIdByName('Group Name');
        $this->addParameter('elementTitle', $this->getText($xpathTR . '//td[' . $cellId . ']'));
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->click($xpathTR . '//td[' . $cellId . ']');
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage();
    }

    /**
     * Delete a Customer Group
     *
     * @param array|string $searchData
     */
    public function deleteCustomerGroup($searchData)
    {
        $this->openCustomerGroup($searchData);
        $this->clickButtonAndConfirm('delete_customer_group', 'confirmation_for_delete');
    }

    /**
     * Edit existing Customer Group
     *
     * @param array $customerGroupData
     * @param array|string $searchData
     */
    public function editCustomerGroup(array $customerGroupData, $searchData)
    {
        $this->openCustomerGroup($searchData);
        $this->fillForm($customerGroupData);
        $this->saveForm('save_customer_group');
    }
}
