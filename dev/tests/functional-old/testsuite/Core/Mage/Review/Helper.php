<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Review
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
class Core_Mage_Review_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Creates review
     *
     * @param array|string $reviewData
     */
    public function createReview($reviewData)
    {
        $reviewData = $this->fixtureDataToArray($reviewData);
        $this->clickButton('add_new_review');
        $product = (isset($reviewData['product_to_review'])) ? $reviewData['product_to_review'] : array();
        if (!$product) {
            $this->fail('Data for selecting product for review is not set');
        }
        $this->searchAndOpen($product, 'select_product_grid', false);
        $this->pleaseWait();
        $this->validatePage();
        $this->fillInfo($reviewData);
        $this->saveForm('save_review');
    }

    /**
     * Edit existing review
     *
     * @param array $reviewData
     * @param array $searchData
     */
    public function editReview(array $reviewData, array $searchData)
    {
        $this->openReview($searchData);
        $this->fillInfo($reviewData);
        $this->saveForm('save_review');
    }

    /**
     * Opens review
     *
     * @param array $reviewSearch
     */
    public function openReview(array $reviewSearch)
    {
        if (isset($reviewSearch['filter_websites']) && !$this->controlIsVisible('dropdown', 'filter_websites')) {
            unset($reviewSearch['filter_websites']);
        }
        $this->searchAndOpen($reviewSearch, 'all_reviews_grid');
    }

    /**
     * Fills tabs in new/edit review
     *
     * @param string|array $reviewData
     */
    public function fillInfo($reviewData)
    {
        if (isset($reviewData['visible_in'])) {
            if ($this->controlIsVisible('multiselect', 'visible_in')) {
                $this->fillMultiselect('visible_in', $reviewData['visible_in']);
                $this->execute(array('script' => "review.updateRating()", 'args' => array()));
                $this->pleaseWait();
            }
            unset($reviewData['visible_in']);
        }
        if (isset($reviewData['product_rating'])) {
            $this->fillRatings($reviewData['product_rating']);
        }
        $this->fillForm($reviewData);
    }

    /**
     * Fills ratings
     *
     * @param array $detailedRatings
     */
    public function fillRatings(array $detailedRatings)
    {
        if ($this->controlIsPresent('message', 'not_available_rating')) {
            $this->fail('Rating is not available for this store view');
        }
        foreach ($detailedRatings as $value) {
            if (!isset($value['rating_name']) || !isset($value['stars'])) {
                $this->fail('Incorrect data to fill');
            }
            $this->addParameter('ratingName', $value['rating_name']);
            $this->addParameter('stars', $value['stars']);
            $this->fillRadiobutton('detailed_rating', 'yes');
        }
    }

    /**
     * Open Review and delete
     *
     * @param array $searchData
     */
    public function deleteReview(array $searchData)
    {
        $this->openReview($searchData);
        $this->clickButtonAndConfirm('delete_review', 'confirmation_for_delete_single_review');
    }

    /**
     * Verify Review
     *
     * @param array|string $reviewData
     * @param array $skipFields
     */
    public function verifyReviewData($reviewData, $skipFields = array())
    {
        $reviewData = $this->fixtureDataToArray($reviewData);
        if (isset($reviewData['visible_in']) && !$this->controlIsVisible('multiselect', 'visible_in')) {
            $skipFields[] = 'visible_in';
        }
        $ratings = (isset($reviewData['product_rating'])) ? $reviewData['product_rating'] : array();
        $this->verifyForm($reviewData, null, $skipFields);
        foreach ($ratings as $ratingData) {
            $this->addParameter('ratingName', $ratingData['rating_name']);
            $this->addParameter('stars', $ratingData['stars']);
            $this->verifyForm(array('detailed_rating' => 'Yes'));
        }
        $this->assertEmptyVerificationErrors();
    }

    #********************************************
    #           Frontend Methods                *
    #********************************************

    /**
     * <p>Create Review</p>
     *
     * @param array|string $reviewData
     * @param bool $validateRating In case $validateRating == TRUE - rating filling will be mandatory
     */
    public function frontendAddReview($reviewData, $validateRating = true)
    {
        $reviewData = $this->fixtureDataToArray($reviewData);
        $linkName = ($this->controlIsVisible('link', 'add_your_review')) ? 'add_your_review' : 'first_review';
        $this->clickControl('link', $linkName, false);
        $this->waitForControlVisible('fieldset', 'add_customer_review');
        $this->waitForControlStopsMoving('fieldset', 'add_customer_review');
        $this->fillFieldset($reviewData, 'add_customer_review');
        if (isset($reviewData['product_rating'])) {
            $this->frontendAddRating($reviewData['product_rating'], $validateRating);
        }
        $this->saveForm('submit_review');
    }

    /**
     * Filling In Rating
     *
     * @param array|string $ratingData
     * @param bool $validateRating
     */
    public function frontendAddRating($ratingData, $validateRating = true)
    {
        $ratingData = $this->fixtureDataToArray($ratingData);
        foreach ($ratingData as $value) {
            $this->addParameter('rateName', $value['rating_name']);
            $this->addParameter('rateId', $value['stars']);
            if (!$this->controlIsVisible('pageelement', 'select_rate')) {
                $this->addVerificationMessage('Rating with name ' . $value['rating_name'] . ' is not on the page');
                continue;
            }
            $this->moveto($this->getControlElement('pageelement', 'select_rate'));
            $this->click();
        }
        if ($validateRating) {
            $this->assertEmptyVerificationErrors();
        }
    }

    /**
     * Review verification after approve
     * (@TODO doesn't work for several reviews posted by one nickname)
     *
     * @param array $verifyData
     */
    public function frontVerifyReviewDisplaying(array $verifyData)
    {
        //$this->addParameter('productName', $productName);
        $nickname = (isset($verifyData['nickname'])) ? $verifyData['nickname'] : '';
        $expectedReview = (isset($verifyData['review'])) ? $verifyData['review'] : '';
        $expectedSummary = (isset($verifyData['summary_of_review'])) ? $verifyData['summary_of_review'] : '';
        $expectedRatings = (isset($verifyData['product_rating'])) ? $verifyData['product_rating'] : array();

        $this->assertTrue($this->controlIsVisible('link', 'reviews'), 'Product does not have approved review(s)');
        $this->clickControl('link', 'reviews', false);
        $this->waitForControlVisible('fieldset', 'add_customer_review');
        $this->waitForControlStopsMoving('fieldset', 'add_customer_review');

        $this->addParameter('reviewerName', $nickname);
        $this->assertTrue(
            $this->controlIsVisible('fieldset', 'customer_review'),
            'Customer with nickname "' . $nickname . '" does not added approved review'
        );
        //Define actual review data
        $actualRatings = array();
        $actualSummary = $this->getControlAttribute(self::FIELD_TYPE_PAGEELEMENT, 'review_summary', 'text');
        $actualReview = $this->getControlAttribute(self::FIELD_TYPE_PAGEELEMENT, 'review_details', 'text');
        if ($this->controlIsVisible('pageelement', 'review_ratings_name')) {
            $values = $this->getControlElements(self::FIELD_TYPE_PAGEELEMENT, 'review_ratings_value');
            $names = $this->getControlElements(self::FIELD_TYPE_PAGEELEMENT, 'review_ratings_name');
            /** @var  $element PHPUnit_Extensions_Selenium2TestCase_Element */
            foreach ($names as $key => $element) {
                $index = $key + 1;
                $actualRatings['rating_' . $index]['rating_name'] = trim($element->text());
                $actualRatings['rating_' . $index]['stars'] = (5 * trim($values[$key]->text())) / 100;
            }
        }
        //Verification on product page
        $this->assertSame($expectedSummary, $actualSummary,
            'Review Summary is not equal to specified: (' . $expectedSummary . ' != ' . $actualSummary . ')');
        $this->assertSame($expectedReview, $actualReview,
            'Review Text is not equal to specified: (' . $expectedReview . ' != ' . $actualReview . ')');
        $this->assertEquals($expectedRatings, $actualRatings, 'Review Rating is not equal to specified');
        //Verification on Review Details page @TODO
//        $this->clickControl('link', 'review_summary');
//        $actualProductName = $this->getControlAttribute('pageelement', 'product_name', 'text');
//        $actualReview = $this->getControlAttribute('pageelement', 'review_details', 'text');
//        $this->assertSame($productName, $actualProductName,
//            "'$productName' product not display on Review Details page");
//        $this->assertSame($expectedReview, $actualReview,
//            "'$expectedReview' review text not display on Review Details page");
//        $this->assertEmptyVerificationErrors();
    }

    /**
     * Verification review on frontend
     * (@TODO doesn't work for several reviews posted by one nickname)
     *
     * @param array $reviewData
     * @param string $productName
     */
    public function frontVerifyReviewDisplayingInMyAccount($reviewData, $productName)
    {
        //Verification in "My Account"
        $this->navigate('customer_account');
        $this->addParameter('productName', $productName);
        $this->assertTrue(
            $this->controlIsPresent('link', 'product_name'),
            "Can not find product with name: $productName in My Recent Reviews block"
        );
        $this->clickControl('link', 'product_name');
        $actualReview = $this->getControlAttribute('pageelement', 'review_details', 'text');
        $this->assertSame($reviewData['review'], $actualReview,
            "'{$reviewData['review']}' review text not display on Review Details page");
        //Verification in "My Account -> My Product Reviews"
        $this->navigate('my_product_reviews');
        $this->assertTrue(
            $this->controlIsPresent('link', 'product_name'),
            "Can not find product with name: $productName in My Product Reviews block"
        );
    }
}
