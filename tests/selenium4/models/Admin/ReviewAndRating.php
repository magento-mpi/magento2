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
     * creating Rating
     *
     * @param array $params May contain the following params:
     * default_value, rating_title_store, store_view_name, store_view_value
     *
     */
    public function createRating($params)
    {
        $this->printDebug('createRating() started...');
        $review_rateData = $params ? $params : $this->review_rateData;
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/ratings');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/reviews_and_ratings/manage_ratings"));
        $this->clickAndWait($this->getUiElement("buttons/add_new"));
        $this->type($this->getUiElement("inputs/default_value"), $review_rateData["default_value"]);
        if (isset($review_rateData['store_view_value'])) {
            $this->type($this->getUiElement("inputs/store_view_value", $review_rateData["store_view_name"]), $review_rateData["store_view_value"]);
        }
        $this->addSelection($this->getUiElement("selectors/visible_in"), "label=regexp:" . $review_rateData["store_view_name"]);
        $this->saveAndVerifyForErrors();
    }

    /**
     * Review approvment
     *
     * @param array $params May contain the following params:
     * search_name
     *
     */
    public function deletingRating($params)
    {
        $this->printDebug('deletingReview() started...');
        $review_rateData = $params ? $params : $this->review_rateData;
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/ratings');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/reviews_and_ratings/manage_ratings"));
        foreach ($review_rateData as $key => $value) {
            if (preg_match('/^search_rating/', $key)) {
                $searchRating[$key] = $value;
            }
        }

        $result = $this->searchAndDoAction('review_container', $searchRating, 'open', NULL);
        if ($result) {
            $this->clickAndWait($this->getUiElement("buttons/delete"));
            if ($this->assertConfirmationPresent('Are you sure you want to do this?')) {
                $this->chooseOkOnNextConfirmation();
            } else {
                $this->printInfo('An error was accured during deleting process');
            }
        }
    }

    /**
     * creating Review
     *
     * @param array $params May contain the following params:
     * SKU, review_container, store_view_name, rating_select, rating_stars,
     * nickname, summary_of_review, review_text, status
     *
     */
    public function createReview($params)
    {
        $this->printDebug('createReview() started...');
        $review_rateData = $params ? $params : $this->review_rateData;
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/reviews/all_review');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/reviews_and_ratings/customer_reviews/all_reviews"));
        $this->clickAndWait($this->getUiElement("buttons/add_new"));
        foreach ($review_rateData as $key => $value) {
            if (preg_match('/^search_product/', $key)) {
                $searchProd[$key] = $value;
            }
        }
        sleep(15);
        $prod_result = $this->searchAndDoAction('product_container', $searchProd, 'open', NULL);
        if ($prod_result) {
            $this->select($this->getUiElement("selectors/status"), $review_rateData["status"]);
            $this->addSelection($this->getUiElement("selectors/visible_in"), "label=regexp:" . $review_rateData["store_view_name"]);
            $this->pleaseWait();
            $this->printDebug($review_rateData["rating_select"] . '_' . $review_rateData["rating_stars"]);
            if ($this->isElementPresent("elements/rating_present")) {
                $this->click($this->getUiElement("selectors/rating_select", $review_rateData["rating_select"] . '_' . $review_rateData["rating_stars"]));
            }
            $this->type($this->getUiElement("inputs/nickname"), $review_rateData["nickname"]);
            $this->type($this->getUiElement("inputs/summary_of_review"), $review_rateData["summary_of_review"]);
            $this->type($this->getUiElement("inputs/review_text"), $review_rateData["review_text"]);
            $this->saveAndVerifyForErrors();
        }
    }

    /**
     * creating Review
     *
     * @param array $params May contain the following params:
     * product_grid, status
     *
     */
    public function ReviewVerification($params)
    {
        $this->printDebug('ReviewVerification() started...');
        $review_rateData = $params ? $params : $this->review_rateData;
        $this->setUiNamespace('admin/pages/catalog/categories/manageproducts');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/manageproducts"));
        foreach ($review_rateData as $key => $value) {
            if (preg_match('/^search_product/', $key)) {
                $searchProd[$key] = $value;
            }
        }
        $prod_result = $this->searchAndDoAction('product_grid', $searchProd, 'open', NULL);
        if ($prod_result) {
            $this->setUiNamespace('admin/pages/catalog/categories/manageproducts/product');
            $this->waitForElement($this->getUiElement("tabs/product_review"), 10);
            $this->click($this->getUiElement("tabs/product_review"));
            $this->pleaseWait();
            foreach ($review_rateData as $key => $value) {
                if (preg_match('/^search_review/', $key)) {
                    $searchReview[$key] = $value;
                }
            }
            $review_result = $this->searchAndDoAction('review_grid', $searchReview, NULL, NULL);
            if ($review_result) {
                $status = $this->getText($this->getUiElement("elements/review_status"));
                $this->printInfo($status);
            }
        }
    }

    /**
     * Review approvment
     *
     * @param array $params May contain the following params:
     * search_title, search_nickname
     *
     */
    public function changeReviewStatus($params)
    {
        $this->printDebug('changeReviewStatus() started...');
        $review_rateData = $params ? $params : $this->review_rateData;
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/reviews/all_review');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/reviews_and_ratings/customer_reviews/all_reviews"));
        foreach ($review_rateData as $key => $value) {
            if (preg_match('/^search_review/', $key)) {
                $searchReview[$key] = $value;
            }
        }
        if ($this->searchAndDoAction('review_container', $searchReview, 'open', NULL)) {
            $this->select($this->getUiElement("selectors/status"), 'label=' . $review_rateData['label']);
            $this->saveAndVerifyForErrors();
        }
    }

    /**
     * Review deleting
     *
     * @param array $params May contain the following params:
     * search_title, search_nickname
     *
     */
    public function deletingReview($params)
    {
        $this->printDebug('deletingReview() started...');
        $review_rateData = $params ? $params : $this->review_rateData;
        $this->setUiNamespace('admin/pages/catalog/reviews_and_ratings/reviews/all_review');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/catalog/reviews_and_ratings/customer_reviews/all_reviews"));
        foreach ($review_rateData as $key => $value) {
            if (preg_match('/^search_review/', $key)) {
                $searchReview[$key] = $value;
            }
        }
        $result = $this->searchAndDoAction('review_container', $searchReview, 'open', NULL);
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