<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_TermsAndConditions
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
class Core_Mage_TermsAndConditions_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Create a Terms and Conditions
     *
     * @param string|array $termsData
     */
    public function createTermsAndConditions($termsData)
    {
        $termsData = $this->fixtureDataToArray($termsData);
        $this->clickButton('create_new_terms_and_conditions');
        if (array_key_exists('store_view', $termsData) && !$this->controlIsPresent('multiselect', 'store_view')) {
            unset($termsData['store_view']);
        }
        $this->fillFieldset($termsData, 'sales_new_condition');
        $this->saveForm('save_condition');
    }

    /**
     * Opens Terms and Conditions
     *
     * @param array $searchData
     */
    public function openTermsAndConditions(array $searchData)
    {
        if (isset($searchData['filter_store_view']) && !$this->controlIsVisible('dropdown', 'filter_store_view')) {
            unset($searchData['filter_store_view']);
        }
        //Search Terms and Conditions
        $searchData = $this->_prepareDataForSearch($searchData);
        $termsLocator = $this->search($searchData, 'sales_checkout_terms_and_conditions_grid');
        $this->assertNotNull($termsLocator, 'Terms and Conditions is not found with data: '
            . print_r($searchData, true));
        $termsRowElement = $this->getElement($termsLocator);
        $termsUrl = $termsRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Condition');
        $cellElement = $this->getChildElement($termsRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineIdFromUrl($termsUrl));
        //Open Terms and Conditions
        $this->url($termsUrl);
        $this->validatePage('edit_condition');
    }

    /**
     * Deletes Terms and Conditions
     *
     * @param array $searchTerms
     */
    public function deleteTerms(array $searchTerms)
    {
        $this->openTermsAndConditions($searchTerms);
        $this->clickButtonAndConfirm('delete_condition', 'confirmation_for_delete');
    }

    /**
     * Deletes All Terms and Conditions in the Grid
     */
    public function deleteAllTerms()
    {
        $termId = $this->getColumnIdByName('Condition');
        while (!$this->controlIsPresent('message', 'no_records_found')) {
            $this->addParameter('columnId', $termId);
            $this->addParameter('elementTitle',
                $this->getControlAttribute('pageelement', 'select_terms_in_grid', 'text'));
            $this->clickControl('pageelement', 'select_terms_in_grid');
            $this->clickButtonAndConfirm('delete_condition', 'confirmation_for_delete');
            $this->assertMessagePresent('success', 'condition_deleted');
        }
    }

    /**
     * Verify entered data
     *
     * @param string $termsData
     */
    public function verifyTermsAndConditions($termsData)
    {
        $termsData = $this->fixtureDataToArray($termsData);
        if (array_key_exists('store_view', $termsData) && !$this->controlIsPresent('multiselect', 'store_view')) {
            unset($termsData['store_view']);
        }
        $this->verifyForm($termsData);
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Get Terms and Conditions ID for checkout
     *
     * @param array $searchData
     *
     * @return string $id
     */
    public function getAgreementId($searchData)
    {
        $xpathTR = $this->search($searchData, 'sales_checkout_terms_and_conditions_grid');
        $this->assertNotNull($xpathTR, 'Terms is not found');
        return trim($this->getElement($xpathTR . '//td[' . $this->getColumnIdByName('ID') . ']')->text());
    }
}