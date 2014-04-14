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
class Core_Mage_Rating_Helper extends Mage_Selenium_AbstractHelper
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
     * @param array $searchData
     */
    public function openRating(array $searchData)
    {
        $searchData = $this->_prepareDataForSearch($searchData);
        $ratingLocator = $this->search($searchData, 'manage_ratings_grid');
        $this->assertNotNull($ratingLocator, 'Rating is not found with data: ' . print_r($searchData, true));
        $ratingRowElement = $this->getElement($ratingLocator);
        $ratingUrl = $ratingRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Rating');
        $cellElement = $this->getChildElement($ratingRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineIdFromUrl($ratingUrl));
        //Open rating
        $this->url($ratingUrl);
        $this->validatePage('edit_rating');
    }

    /**
     * Fills tabs in new/edit rating
     *
     * @param string|array $ratingData
     */
    public function fillTabs($ratingData)
    {
        $ratingData = $this->fixtureDataToArray($ratingData);
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
        $this->clickButtonAndConfirm('delete_rating', 'confirmation_for_single_delete');
    }

    /**
     * Verify Rating
     *
     * @param array|string $ratingData
     */
    public function verifyRatingData($ratingData)
    {
        $ratingData = $this->fixtureDataToArray($ratingData);
        $titles = (isset($ratingData['store_view_titles'])) ? $ratingData['store_view_titles'] : array();
        $this->verifyForm($ratingData);

        foreach ($titles as $value) {
            $this->addParameter('storeViewName', $value['store_view_name']);
            $this->verifyForm(array('store_view_title' => $value['store_view_title']));
        }
        $this->assertEmptyVerificationErrors();
    }
}