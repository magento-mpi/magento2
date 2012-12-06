<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Rating
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
class Core_Mage_Rating_Helper extends Mage_Selenium_TestCase
{
    /**
     * Creates rating
     *
     * @param $ratingData
     */
    public function createRating($ratingData)
    {
        $this->clickButton('add_new_rating');
        $this->fillTabs($ratingData);
        $this->saveForm('save_rating');
    }

    /**
     * Edit existing rating
     *
     * @param $ratingData
     * @param $searchData
     */
    public function editRating($ratingData, $searchData)
    {
        $this->openRating($searchData);
        $this->fillTabs($ratingData);
        $this->saveForm('save_rating');
    }

    /**
     * Opens rating
     *
     * @param array $ratingSearch
     */
    public function openRating(array $ratingSearch)
    {
        $xpathTR = $this->search($ratingSearch, 'manage_ratings_grid');
        $this->assertNotEquals(null, $xpathTR, 'Rating is not found');
        $param = $this->getText($xpathTR . '/td[' . $this->getColumnIdByName('Rating Name') . ']');
        $this->addParameter('elementTitle', $param);
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->click($xpathTR);
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage();
    }

    /**
     * Fills tabs in new/edit rating
     *
     * @param string|array $ratingData
     */
    public function fillTabs($ratingData)
    {
        if (is_string($ratingData)) {
            $elements = explode('/', $ratingData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $ratingData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $this->fillForm($ratingData);
        if (isset($ratingData['store_view_titles'])) {
            $this->fillRatingTitles($ratingData['store_view_titles']);
        }
    }

    /**
     * Fills rating titles for each store view
     *
     * @param array $storeViewTitles
     */
    public function fillRatingTitles(array $storeViewTitles)
    {
        foreach ($storeViewTitles as $value) {
            if (isset($value['store_view_name']) && isset($value['store_view_title'])) {
                $this->addParameter('storeViewName', $value['store_view_name']);
                $this->fillField('store_view_title', $value['store_view_title']);
            } else {
                $this->fail('Incorrect data to fill');
            }
        }
    }

    /**
     * Open Rating and delete
     *
     * @param array $searchData
     */
    public function deleteRating(array $searchData)
    {
        $this->openRating($searchData);
        $this->clickButtonAndConfirm('delete_rating', 'confirmation_for_delete');
    }

    /**
     * Verify Rating
     *
     * @param array|string $ratingData
     */
    public function verifyRatingData($ratingData)
    {
        if (is_string($ratingData)) {
            $elements = explode('/', $ratingData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $ratingData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $titles = (isset($ratingData['store_view_titles'])) ? $ratingData['store_view_titles'] : array();
        $this->verifyForm($ratingData);

        foreach ($titles as $value) {
            $this->addParameter('storeViewName', $value['store_view_name']);
            $this->verifyForm(array('store_view_title' => $value['store_view_title']));
        }
        $this->assertEmptyVerificationErrors();
    }
}