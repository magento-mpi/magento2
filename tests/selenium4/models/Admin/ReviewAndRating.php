<?php

/**
 * Admin customer framework model
 *
 * @author Magento Inc.
 */
class Model_Admin_ReviewAndRating extends Model_Admin {

    public function loadConfigData()
    {
        parent::loadConfigData();
        $this->Data = array();
    }

    public function fillRatingTitleForStore($storeName, $title)
    {
        // Fill in Rating Title for store
        if ($this->isElementPresent($this->getUiElement('inputs/title_for_store', $storeName))) {
            $this->type($this->getUiElement('inputs/title_for_store', $storeName), $title);
        } else {
            $this->printInfo("You cannot specify a title for '" . $storeName . "' store view");
        }
    }

    public function setElementVisible($storeName)
    {
        //Enable display for store view
        if ($this->isElementPresent($this->getUiElement('selectors/visible_in') .
                        "//option[contains(.,'" . $storeName . "')]")) {
            $this->addSelection($this->getUiElement('selectors/visible_in'), "label=regexp:" . $storeName);
        } else {
            $this->printInfo("You cannot turn on the display of ratings for the '" . $storeName . "' store view");
        }
    }

    public function doDeleteElement($confirmation)
    {
        $result = TRUE;
        if ($this->isElementPresent($this->getUiElement('/admin/global/buttons/delete'))) {
            $this->chooseCancelOnNextConfirmation();
            $this->click($this->getUiElement('/admin/global/buttons/delete'));
            if ($this->isConfirmationPresent()) {
                $text = $this->getConfirmation();
                if ($text == $confirmation) {
                    $this->chooseOkOnNextConfirmation();
                    $this->click($this->getUiElement('/admin/global/buttons/delete'));
                } else {
                    $this->printInfo('The confirmation text incorrect: ' . $text);
                    $result = FALSE;
                }
            } else {
                $this->printInfo('The confirmation does not appear');
            }
            if ($result) {
                if ($this->waitForElement($this->getUiElement('/admin/messages/error'), 20)) {
                    $etext = $this->getText($this->getUiElement('/admin/messages/error'));
                    $this->setVerificationErrors($etext);
                } elseif ($this->waitForElement($this->getUiElement('/admin/messages/success'), 30)) {
                    $etext = $this->getText($this->getUiElement('/admin/messages/success'));
                    $this->printInfo($etext);
                } else {
                    $this->setVerificationErrors('No success message');
                }
            }
        } else {
            $this->printInfo("There is no way to remove an item(There is no 'Delete' button)");
        }
        return $result;
    }

    /**
     * Create Rating
     *
     * @param array $params May contain the following params:
     * rating_title_store, store_view_value
     */
    public function doCreateRating($params)
    {
        //Data preparation
        $Data = $params ? $params : $this->Data;
        $defTitle = $this->isSetValue($params, 'default_title');
        $storeTitle = $this->isSetValue($params, 'title_for_store');
        $storeNameTitle = $this->isSetValue($params, 'store_view_name_title');
        $storeNameVisible = $this->isSetValue($params, 'store_view_name_visible');

        // Open manage ratings page
        $this->clickAndWait($this->getUiElement('/admin/topmenu/catalog/reviews_and_ratings/manage_ratings'));
        // Add new rating
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_ratings');
        $this->clickAndWait($this->getUiElement('buttons/add_new_rating'));
        // Fill in Default Rating Title
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_ratings/ratings');
        $this->type($this->getUiElement('inputs/title_default'), $defTitle);
        // Fill in Rating Title(s) for store(s)
        if (is_array($storeTitle) and is_array($storeNameTitle)) {
            $qtyFields = count($storeNameTitle);
            for ($y = 0; $y < $qtyFields; $y++) {
                $this->fillRatingTitleForStore($storeNameTitle[$y], $storeTitle[$y]);
            }
        } elseif ($storeNameTitle != NULL) {
            $this->fillRatingTitleForStore($storeNameTitle, $storeTitle);
        }
        //Enable display for store view(s)
        if (is_array($storeNameVisible)) {
            $qtyStores = count($storeNameVisible);
            for ($y = 0; $y < $qtyStores; $y++) {
                $this->setElementVisible($storeNameVisible[$y]);
            }
        } elseif ($storeNameVisible != Null) {
            $this->setElementVisible($storeNameVisible);
        }
        // Save
        $this->saveAndVerifyForErrors();
    }

    /**
     * Delete Rating
     *
     * @param array $deleteRating
     */
    public function doDeleteRating($deleteRating)
    {
        $this->clickAndWait($this->getUiElement('/admin/topmenu/catalog/reviews_and_ratings/manage_ratings'));
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_ratings');
        $searchResult = $this->searchAndDoAction('rating_container', $deleteRating, "open", NULL);
        if ($searchResult) {
            $confirmation = 'Are you sure you want to do this?';
            $deleteResult = $this->doDeleteElement($confirmation);
            if (!$deleteResult) {
                $this->printInfo('Element is not deleleted');
            }
        }
    }

    /**
     * Setting rating stars
     * 
     * @param <type> $productRatingStars 
     */
    public function ratingSet($productRatingStars)
    {
        //Select stars for each rating
        $this->waitForElement("elements/rating_present", 1);
        if ($this->isElementPresent($this->getUiElement("elements/rating_present"))) {
            $this->click($this->getUiElement("selectors/rating_select", $productRatingStars));
        } else {
            $this->printInfo("There are no such ratings ");
        }
    }

    /**
     * creating Review
     *
     * @param array $params May contain the following params:
     * review_container, rating_select, rating_stars,
     * nickname, summary_of_review, review_text, status
     *
     */
    public function createReview($params)
    {
        //Data preparation
        $storeNameVisible = $this->isSetValue($params, 'store_view_name_visible');
        $productRatingStars = $this->isSetValue($params, 'rating_stars');

        $this->printDebug('createReview() started...');
        $review_rateData = $params ? $params : $this->review_rateData;
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_review');
        // Open manage review page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/reviews_and_ratings/customer_reviews/all_reviews"));
        // Add new review
        $this->clickAndWait($this->getUiElement("buttons/add_new"));
        //search product for review
        foreach ($review_rateData as $key => $value) {
            if (preg_match('/^search_product/', $key)) {
                $searchProd[$key] = $value;
            }
        }
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_review/edit_review_page');
        $prod_result = $this->searchAndDoAction('product_container', $searchProd, 'open', NULL);
        if ($prod_result) {
            //Set up review status
            $this->select($this->getUiElement("selectors/status"), $review_rateData["status"]);
            //Enable display for store view(s)
            if (is_array($storeNameVisible)) {
                $qtyStores = count($storeNameVisible);
                for ($y = 0; $y < $qtyStores; $y++) {
                    $this->setElementVisible($storeNameVisible[$y]);
                }
            } elseif ($storeNameVisible != Null) {
                $this->setElementVisible($storeNameVisible);
            }
            //select ratings stars
            if (is_array($productRatingStars)) {
                $qtyRatings = count($productRatingStars);
                for ($y = 0; $y < $qtyRatings; $y++) {
                    $this->ratingSet($productRatingStars[$y]);
                }
            } elseif ($productRatingStars != Null) {
                $this->ratingSet($productRatingStars);
            }
            //fill all fields
            $this->type($this->getUiElement("inputs/nickname"), $review_rateData["nickname"]);
            $this->type($this->getUiElement("inputs/summary_of_review"), $review_rateData["summary_of_review"]);
            $this->type($this->getUiElement("inputs/review_text"), $review_rateData["review_text"]);
            //saving review
            $this->saveAndVerifyForErrors();
        }
    }

    /**
     * verification Review
     *
     * @param array $params May contain the following params:
     * product_grid, status
     *
     */
    public function ReviewVerification($params)
    {
        $this->printDebug('ReviewVerification() started...');
        //Data preparation
        $review_rateData = $params ? $params : $this->review_rateData;
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts');
        //Open manage products page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/manageproducts"));
        //Searching for product
        foreach ($review_rateData as $key => $value) {
            if (preg_match('/^search_product/', $key)) {
                $searchProd[$key] = $value;
            }
        }
        $prod_result = $this->searchAndDoAction('product_grid', $searchProd, 'open', NULL);
        if ($prod_result) {
            $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
            //select product review tab
            $this->waitForElement($this->getUiElement("tabs/product_review"), 10);
            $this->click($this->getUiElement("tabs/product_review"));
            $this->pleaseWait();
            //searching for review
            foreach ($review_rateData as $key => $value) {
                if (preg_match('/^search_review/', $key)) {
                    $searchReview[$key] = $value;
                }
            }
            $review_result = $this->searchAndDoAction('review_grid', $searchReview, NULL, NULL);
            if ($review_result) {
                //getting review status
                $status = $this->getText($this->getUiElement("elements/review_status"));
                $this->printInfo($status);
            }
        }
    }

    /**
     * Review approvment
     *
     * @param array $params May contain the following params:
     * 
     */
    public function changeReviewStatus($params)
    {
        $this->printDebug('changeReviewStatus() started...');
        $review_rateData = $params ? $params : $this->review_rateData;
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/edit_review_page');
        //open manage review page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/reviews_and_ratings/customer_reviews/all_reviews"));
        //search for review
        foreach ($review_rateData as $key => $value) {
            if (preg_match('/^search_review/', $key)) {
                $searchReview[$key] = $value;
            }
        }
        if ($this->searchAndDoAction('review_container', $searchReview, 'open', NULL)) {
            //changing status
            $this->select($this->getUiElement("selectors/status"), 'label=' . $review_rateData['label']);
            $this->saveAndVerifyForErrors();
        }
    }

    /**
     * Review deleting
     *
     * @param array $params May contain the following params:
     * 
     */
    public function deletingReview($params)
    {
        $this->printDebug('deletingReview() started...');
        $review_rateData = $params ? $params : $this->review_rateData;
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/all_review');
        //open manage review page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/reviews_and_ratings/customer_reviews/all_reviews"));
        //search for review
        foreach ($review_rateData as $key => $value) {
            if (preg_match('/^search_review/', $key)) {
                $searchReview[$key] = $value;
            }
        }
        $result = $this->searchAndDoAction('review_container', $searchReview, 'open', NULL);
        //deleating review
        if ($result) {
            $this->clickAndWait($this->getUiElement("buttons/delete"));
            if ($this->assertConfirmationPresent('Are you sure you want to do this?')) {
                $this->chooseOkOnNextConfirmation();
            } else {
                $this->printInfo('An error was accured during deleting process');
            }
        }
    }

}