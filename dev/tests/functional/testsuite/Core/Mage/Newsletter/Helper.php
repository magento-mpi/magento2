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
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Newsletter_Helper extends Mage_Selenium_TestCase
{
    /**
     * Subscribe to newsletter
     *
     * @param string $email
     */
    public function frontSubscribe($email)
    {
        $this->fillField('sign_up_newsletter', $email);
        $this->saveForm('subscribe');
    }

    /**
     * Perform a mass action with newsletter subscribers
     *
     * @param string $action Mass action value: 'unsubscribe'|'delete'
     * @param array $searchDataSet
     */
    public function massAction($action, $searchDataSet)
    {
        foreach ($searchDataSet as $searchData) {
            $this->searchAndChoose($searchData);
        }
        $this->addParameter('qtyOfRecords', count($searchDataSet));
        $this->fillDropdown('subscribers_massaction', ucfirst(strtolower($action)));
        $this->clickButton('submit');
    }

    /**
     * Perform a mass action with newsletter subscribers
     *
     * @param string $status Status from data set to check, e.g. 'subscribed'|'unsubscribed'
     * @param array $searchData
     *
     * @return boolean. True if $searchData with $status status is found. False otherwise.
     */
    public function checkStatus($status, $searchData)
    {
        $searchData['filter_status'] = ucfirst(strtolower($status));
        return !is_null($this->search($searchData));
    }
}