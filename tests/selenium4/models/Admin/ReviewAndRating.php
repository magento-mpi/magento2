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
    public function ratingSet($nameRating, $valueRating)
    {
        //Select stars for each rating
        $this->waitForElement($this->getUiElement("elements/ratings_not_empty"), 1);
        if ($this->isElementPresent($this->getUiElement("elements/rating_present", $nameRating))) {
            $inputRating = $nameRating . "_" . $valueRating;
            $this->click($this->getUiElement("selectors/rating_select", $inputRating));
        } else {
            $this->printInfo("\r\n Rating " . $nameRating . " is defined incorect");
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
        $productRatingForStars = $this->isSetValue($params, 'rating_for_stars');
        $productRatingStars = $this->isSetValue($params, 'rating_stars');

        $this->printDebug('createReview() started...');
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_review');
        // Open manage review page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/reviews_and_ratings/customer_reviews/all_reviews"));
        // Add new review
        $this->clickAndWait($this->getUiElement("buttons/add_new"));
        //search product for review
        foreach ($Data as $key => $value) {
            if (preg_match('/^search_product/', $key)) {
                $searchProd[$key] = $value;
            }
        }
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_review/edit_review_page');
        $prod_result = $this->searchAndDoAction('product_container', $searchProd, 'open', NULL);
        if ($prod_result) {
            //Set up review status
            $this->checkAndSelectField($params, "status", NULL);
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
            $this->waitForElement($this->getUiElement("elements/rating_qty"), 1);
            $ratings_exist = ($this->getXpathCount($this->getUiElement("elements/rating_qty")));
            $ratings_defined = count($productRatingForStars);
            if ($ratings_exist == $ratings_defined) {
                if (is_array($productRatingForStars)) {
                    $ratings_defined = count($productRatingForStars);
                    for ($y = 0; $y < $ratings_defined; $y++) {
                        $this->ratingSet($productRatingForStars[$y], $productRatingStars[$y]);
                    }
                } elseif ($productRatingForStars != Null) {
                    $this->ratingSet($productRatingForStars, $productRatingStars);
                }
            } elseif ($ratings_exist != 0) {
                $this->printInfo("\r\n Wrong q-ty of ratings ");
                $this->printInfo("\r\n Q-ty of rating that exist is:  " . $ratings_exist . " !");
                $this->printInfo("\r\n Q-ty of defined ratings is:  " . $ratings_defined . " !");
            } elseif ($ratings_defined != 0) {
                $this->printInfo("\r\n There are no such ratings ");
            }
            //fill all fields
            $this->type($this->getUiElement("inputs/nickname"), $Data["nickname"]);
            $this->type($this->getUiElement("inputs/summary_of_review"), $Data["summary_of_review"]);
            $this->type($this->getUiElement("inputs/review_text"), $Data["review_text"]);
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
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts');
        //Open manage products page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/manageproducts"));
        //Searching for product
        foreach ($Data as $key => $value) {
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
            foreach ($Data as $key => $value) {
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
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_review/edit_review_page');
        //open manage review page
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/reviews_and_ratings/customer_reviews/all_reviews"));
        //search for review
        foreach ($Data as $key => $value) {
            if (preg_match('/^search_review/', $key)) {
                $searchReview[$key] = $value;
            }
        }
        if ($this->searchAndDoAction('review_container', $searchReview, 'open', NULL)) {
            //changing status
            $this->checkAndSelectField($params, "status", NULL);
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
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/reviews_and_ratings/customer_reviews/all_reviews"));
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/manage_review/edit_review_page');
        $searchResult = $this->searchAndDoAction('review_container', $deleteReview, "open", NULL);
        if ($searchResult) {
            $confirmation = 'Are you sure you want to do this?';
            $deleteResult = $this->doDeleteElement($confirmation);
            if (!$deleteResult) {
                $this->printInfo('Element is not deleleted');
            }
        }
    }

}