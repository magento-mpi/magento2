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
class Core_Mage_Newsletter_Helper extends Mage_Selenium_AbstractHelper
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
        if ($this->getCurrentPage() == 'home_page') {
            $this->markTestIncomplete('BUG: Redirect to home page after add subscription');
        }
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
            $this->searchAndChoose($searchData, 'subscribers_grid');
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
        return !is_null($this->search($searchData, 'subscribers_grid'));
    }

    /**
     * Create Newsletter Templates
     * Preconditions: 'New Newsletter Template' page is opened.
     *
     * @param array|string $newsletterData
     */
    public function createNewsletterTemplate($newsletterData)
    {
        $newsletterData = $this->fixtureDataToArray($newsletterData);
        if (empty($newsletterData)) {
            $this->fail('$newsletterData parameter is empty');
        }
        $this->clickButton('add_new_template');
        $this->fillNewsletterForm($newsletterData);
        $this->saveForm('save_template');
    }

    /**
     * <p>Fill fields in Newsletter form according to the resulting array</p>
     *
     * @param array $newsletterData
     * @param string
     */
    public function fillNewsletterForm(array $newsletterData, $fieldName = 'newsletter_edit_form')
    {
        if (empty($newsletterData)) {
            return;
        }
        if (isset($newsletterData['newsletter_content_data'])) {

            if ($fieldName == 'newsletter_edit_form') {
                $this->clickButtonAndConfirm('convert_to_plain_text', 'confirmation_convert_to_plain_text', false);
            } else {
                $this->clickButton('show_hide_editor', false);
                $this->waitForControlEditable('field', 'newsletter_content_data');
            }
        }
        $this->fillFieldset($newsletterData, $fieldName);
    }

    /**
     * <p>Edit Newsletter template</p>
     *
     * @param array $dataForSearch
     * @param array $newNewsData
     */
    public function editNewsletter(array $dataForSearch, array $newNewsData)
    {
        if (empty($dataForSearch)) {
            $this->fail('$dataForSearch parameter is empty');
        }
        if (empty($newNewsData)) {
            $this->fail('$newNewsData parameter is empty');
        }
        $this->openNewsletter($this->convertToFilter($dataForSearch));
        $this->fillNewsletterForm($newNewsData);
        $this->clickButton('save_template');
    }

    /**
     * <p>Convert method. Get newsletter array and convert it to filter array for search</p>
     *
     * @param array $dataForSearch
     *
     * @return array
     */
    public function convertToFilter(array $dataForSearch)
    {
        if (empty($dataForSearch)) {
            return array();
        }

        $searchData = array();
        foreach ($dataForSearch as $key => $value) {
            if (preg_match('/^newsletter/', $key)) {
                $strArr = explode('_', $key);
                if (isset($strArr[0]) && $strArr[0] == 'newsletter') {
                    $strArr[0] = 'filter';
                }
                $key = implode('_', $strArr);
                $searchData[$key] = $value;
            }
        }
        if (isset($searchData['filter_template_sender_name'])) {
            unset($searchData['filter_template_sender_name']);
        }
        if (isset($searchData['filter_template_sender_email'])) {
            $searchData['filter_template_sender'] = $searchData['filter_template_sender_email'];
            unset($searchData['filter_template_sender_email']);
        }
        if (isset($searchData['filter_content_data'])) {
            unset($searchData['filter_content_data']);
        }
        return $searchData;
    }

    /**
     * <p>Put exists Newsletter in to queue</p>
     *
     * @param array $newsData
     * @param array $newData
     */
    public function putNewsToQueue(array $newsData, array $newData = array())
    {
        if (empty($newsData)) {
            $this->fail('$newNewsData parameter is empty');
        }
        $newsletterXpath = $this->search($this->convertToFilter($newsData), 'newsletter_templates_grid');
        $parentXpath = $this->_getControlXpath('fieldset', 'newsletter_templates_grid');
        $newsletterXpath = str_replace($parentXpath, '', $newsletterXpath);
        $this->addParameter('prexpath', $newsletterXpath);
        $this->fillDropdown('queue_newsletter', 'Queue Newsletter...');
        $this->waitForPageToLoad();
        $this->addParameter('template_id', $this->defineIdFromUrl());
        $this->validatePage('newsletter_queue_edit');
        $this->fillNewsletterForm($newData, 'queue_edit_form');
        $this->saveForm('save_newsletter');
    }

    /**
     * <p>Delete Newsletter template</p>
     *
     * @param array $newNewsData
     */
    public function deleteNewsletter(array $newNewsData)
    {
        if (empty($newNewsData)) {
            $this->fail('$newNewsData parameter is empty');
        }
        $this->openNewsletter($this->convertToFilter($newNewsData));
        $this->clickButtonAndConfirm('delete_template', 'confirmation_for_delete');
    }

    /**
     * @param array $searchData
     */
    public function openNewsletter(array $searchData)
    {
        //Search Newsletter
        $searchData = $this->_prepareDataForSearch($searchData);
        $newsletterLocator = $this->search($searchData, 'newsletter_templates_grid');
        $this->assertNotNull($newsletterLocator, 'Newsletter is not found with data: ' . print_r($searchData, true));
        $newsletterRowElement = $this->getElement($newsletterLocator);
        $newsletterUrl = $newsletterRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Template');
        $cellElement = $this->getChildElement($newsletterRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineIdFromUrl($newsletterUrl));
        //Open Newsletter
        $this->url($newsletterUrl);
        $this->validatePage('edit_news_template');
    }
}