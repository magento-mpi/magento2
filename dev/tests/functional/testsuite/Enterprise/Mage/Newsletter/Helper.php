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
 * @package     Mage_Newsletter
 * @subpackage  functional_tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @method Community2_Mage_Newsletter_Helper helper(string $className)
 */
class Enterprise_Mage_Newsletter_Helper extends Core_Mage_Newsletter_Helper
{
    /**
     * Create Newsletter Templates
     * Preconditions: 'New Newsletter Template' page is opened.
     *
     * @param array|string $newsletterData
     */
    public function createNewsletterTemplate($newsletterData)
    {
        $this->helper('Community2/Mage/Newsletter/Helper')->createNewsletterTemplate($newsletterData);
    }

    /**
     * <p>Fill fields in Newsletter form according to the resulting array</p>
     *
     * @param array $newsletterData
     * @param string
     */
    public function fillNewsletterForm(array $newsletterData, $fieldName = 'newsletter_edit_form')
    {
        $this->helper('Community2/Mage/Newsletter/Helper')->fillNewsletterForm($newsletterData, $fieldName);
    }

    /**
     * <p>Edit Newsletter template</p>
     *
     * @param array $dataForSearch
     * @param array $newNewsData
     */
    public function editNewsletter(array $dataForSearch, array $newNewsData)
    {
        $this->helper('Community2/Mage/Newsletter/Helper')->editNewsletter($dataForSearch, $newNewsData);
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
        return $this->helper('Community2/Mage/Newsletter/Helper')->convertToFilter($dataForSearch);
    }

    /**
     * <p>Put exists Newsletter in to queue</p>
     *
     * @param array $newsData
     * @param array $newData
     */
    public function putNewsToQueue(array $newsData, array $newData = array())
    {
        $this->helper('Community2/Mage/Newsletter/Helper')->putNewsToQueue($newsData, $newData);
    }

    /**
     * <p>Delete Newsletter template</p>
     *
     * @param array $newNewsData
     */
    public function deleteNewsletter(array $newNewsData)
    {
        $this->helper('Community2/Mage/Newsletter/Helper')->deleteNewsletter($newNewsData);
    }
}
