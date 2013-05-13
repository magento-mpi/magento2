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
     * @param array $ratingSearch
     */
    public function openRating(array $ratingSearch)
    {
        $xpathTR = $this->search($ratingSearch, 'manage_ratings_grid');
        $this->assertNotEquals(null, $xpathTR, 'Rating is not found');
        $cellId = $this->getColumnIdByName('Rating Name');
        $this->addParameter('tableLineXpath', $xpathTR);
        $this->addParameter('cellIndex', $cellId);
        $param = $this->getControlAttribute('pageelement', 'table_line_cell_index', 'text');
        $this->addParameter('elementTitle', $param);
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->clickControl('pageelement', 'table_line_cell_index');
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