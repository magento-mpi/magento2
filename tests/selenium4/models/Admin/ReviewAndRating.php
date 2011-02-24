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

    /**
     *
     * @param <type> $storeTitles 
     */
    public function fillRatingTitleForStore($storeTitles)
    {
        // Fill in Rating Title for store
        if ($storeTitles != NULL) {
            foreach ($storeTitles as $key => $value) {
                if ($this->isElementPresent($this->getUiElement('inputs/title_for_store', $key))) {
                    $this->type($this->getUiElement('inputs/title_for_store', $key), $value);
                } else {
                    $this->printInfo("You cannot specify a title for '" . $key . "' store view");
                }
            }
        }
    }

    /**
     * add store view(s) for Rating Visibility
     *
     * @param <type> $storeViewName
     */
    public function setRatingVisibility($storeViewName)
    {
        if ($storeViewName != NULL) {
            foreach ($storeViewName as $value) {
                //Enable display for store view
                if ($this->isElementPresent($this->getUiElement('inputs/visible_in') .
                                "//option[contains(.,'" . $value . "')]")) {
                    $this->addSelection($this->getUiElement('inputs/visible_in'), "label=regexp:\\s+" . $value);
                } else {
                    $this->printInfo("You cannot turn on the display of ratings for the '" . $value . "' store view");
                }
            }
        }
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
        $storeTitles = $this->isSetValue($params, 'title_for_stores');
        $storeViewNameVisible = $this->isSetValue($params, 'store_view_name_visible');

        // Open manage ratings page
        $this->navigate('Catalog/Reviews and Ratings/Manage Ratings');

        // Add new rating
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_ratings');
        $this->clickAndWait($this->getUiElement('buttons/add_new_rating'));
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_ratings/ratings');
        // Fill in Default Rating Title and Rating Title(s) for store(s)
        $this->fillRatingTitleForStore($storeTitles);
        //Enable display for store view(s)
        $this->setRatingVisibility($storeViewNameVisible);
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
        $this->navigate('Catalog/Reviews and Ratings/Manage Ratings');
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_ratings');
        $searchResult = $this->searchAndDoAction('rating_container', $deleteRating, "open", NULL);
        if ($searchResult) {
            $deleteResult = $this->doDeleteElement();
            if (!$deleteResult) {
                $this->printInfo('Element is not deleleted');
            }
        }
    }

    /**
     * Setting ratings stars
     * 
     * @param <type> $productRatingStars 
     */
    public function setRatings($params)
    {
        $productRating = $this->isSetValue($params, 'product_rating');
        //Select stars for each rating
        if ($productRating != NULL) {
            foreach ($productRating as $key => $value) {
                if ($this->isElementPresent($this->getUiElement("elements/rating_present", $key))) {
                    $inputRating = $key . "_" . $value;
                    $this->click($this->getUiElement("inputs/rating_select", $inputRating));
                } else {
                    $this->printInfo("\r\n Rating " . $key . " is defined incorect");
                }
            }
        }
    }

    /**
     *
     * @param <type> $storeViewName 
     */
    public function setReviewVisibility($storeViewName)
    {
        if ($storeViewName != NULL) {
            foreach ($storeViewName as $value) {
                //Enable display for store view
                if ($this->isElementPresent($this->getUiElement('inputs/visible_in') .
                                "//option[contains(.,'" . $value . "')]")) {
                    $this->addSelection($this->getUiElement('inputs/visible_in'), "label=regexp:\\s+" . $value);
                    $this->pleaseWait();
                } else {
                    $this->printInfo("You cannot turn on the display of ratings for the '" . $value . "' store view");
                }
            }
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
    public function doCreateReview($params)
    {
        //Data preparation
        $storeViewName = $this->isSetValue($params, 'store_view_name_visible');

        $this->navigate('Catalog/Reviews and Ratings/Customer Reviews/All Reviews');

        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_review');
        // Add new review
        $this->clickAndWait($this->getUiElement("buttons/add_new"));
        //search product for review
        $this->setUiNamespace('admin/pages/catalog/manage_products');
        $searchWord = '/^search_product/';
        $searchProd = $this->dataPreparation($params, $searchWord);
        $prod_result = $this->searchAndDoAction('product_grid', $searchProd, 'open', NULL);
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_review/edit_review');
        if ($prod_result) {
            //Enable display for store view(s)
            $this->setReviewVisibility($storeViewName);
            //select ratings stars
            if (!$this->isElementPresent($this->getUiElement('elements/rating_disabled'))) {
                $this->setRatings($params);
            }
            // Select review status
            $this->checkAndSelectField($params, "status");
            // Fillin  fields: Nickname,  Summary of Review, Review
            $this->checkAndFillField($params, 'nickname', Null);
            $this->checkAndFillField($params, 'summary_of_review', Null);
            $this->checkAndFillField($params, 'review_text', Null);
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
        $this->navigate('Catalog/Manage Products');

        //Search  product
        $searchWord = '/^search_product/';
        $this->setUiNamespace('admin/pages/catalog/manage_products');
        $searchProd = $this->dataPreparation($params, $searchWord);
        $searchResult = $this->searchAndDoAction('product_grid', $searchProd, 'open', NULL);
        if ($searchResult) {
            $this->setUiNamespace('admin/pages/catalog/manage_products/product');
            //select product review tab
            $this->click($this->getUiElement("tabs/product_review"));
            $this->pleaseWait();
            //searching for review
            $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_review');
            $searchWord = '/^search_review/';
            $searchReview = $this->dataPreparation($params, $searchWord);
            $review_result = $this->searchAndDoAction('review_container', $searchReview, NULL, NULL);
            if ($review_result) {
                $this->printInfo('Review for this product is added');
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
        $this->navigate('Catalog/Reviews and Ratings/Customer Reviews/All Reviews');

        //search for review
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_review');
        $searchWord = '/^search_review_/';
        $searchReview = $this->dataPreparation($params, $searchWord);
        $searchResult = $this->searchAndDoAction('review_container', $searchReview, 'open', NULL);
        if ($searchResult) {
            $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_review/edit_review');
            //changing status
            $this->checkAndSelectField($params, "status");
            $this->saveAndVerifyForErrors();
        }
    }

    /**
     * Delete Review
     *
     * @param array $deleteReview
     */
    public function doDeleteReview($deleteReview)
    {
        $this->navigate('Catalog/Reviews and Ratings/Customer Reviews/All Reviews');

        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_review');
        $searchResult = $this->searchAndDoAction('review_container', $deleteReview, "open", NULL);
        if ($searchResult) {
            $deleteResult = $this->doDeleteElement();
            if (!$deleteResult) {
                $this->printInfo('Element is not deleleted');
            }
        }
    }

}