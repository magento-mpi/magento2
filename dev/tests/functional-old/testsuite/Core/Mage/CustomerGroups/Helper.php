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
class Core_Mage_CustomerGroups_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Create new Customer Group
     *
     * @param array|string $customerGroupData
     */
    public function createCustomerGroup($customerGroupData)
    {
        $customerGroupData = $this->fixtureDataToArray($customerGroupData);
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
        //Search Customer Group
        $searchData = $this->fixtureDataToArray($searchData);
        $searchData = $this->_prepareDataForSearch($searchData);
        $groupLocator = $this->search($searchData, 'customer_group_grid');
        $this->assertNotNull($groupLocator, 'Customer Group is not found with data: ' . print_r($searchData, true));
        $groupRowElement = $this->getElement($groupLocator);
        $groupUrl = $groupRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Group');
        $cellElement = $this->getChildElement($groupRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineIdFromUrl($groupUrl));
        //Open Customer Group
        $this->url($groupUrl);
        $this->validatePage('edit_customer_group');
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