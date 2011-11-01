<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tags_Helper extends Mage_Selenium_TestCase
{

    /**
     * <p>Create Tag</p>
     *
     * @param string|array $tagName
     */
    public function frontendAddTag($tagName, $loggedIn = TRUE)
    {
        if (is_array($tagName) && array_key_exists('new_tag_names', $tagName)) {
            $tagName = $tagName['new_tag_names'];
        } else {
            $this->fail('Array key is absent in array');
        }
        $tagNameArray = $this->convertStringToArray($tagName);
        $tagQty = count($tagNameArray);
        $this->addParameter('tagQty', $tagQty);
        if (!$this->controlIsPresent('field', 'input_new_tags')) {
            $this->fail('Element is absent on the page');
        }
        $this->fillForm(array('input_new_tags' => $tagName));
        $this->clickButton('add_tags');
        if ($loggedIn) {
            $this->assertFalse($this->checkCurrentPage('customer_login'), 'Got on Login Page');
        } else {
            $this->assertTrue($this->checkCurrentPage('customer_login'), 'Did not get on Login Page');
        }
    }

    /**
     * Correct string with tags for verification
     *
     * @param string $tagName
     * @return array
     */
    public function convertStringToArray($tagName)
    {
        $tagNameArray = array_filter(explode("\n", preg_replace("/(\'(.*?)\')|(\s+)/i", "$1\n", $tagName)), 'strlen');
        foreach ($tagNameArray as $key => $value) {
            $tags[$key] = trim($value, " \x22\x27");
            $tags[$key] = htmlspecialchars($tags[$key]);
        }
        return $tags;
    }
    /**
     * Verification tags on frontend
     *
     * @param array $verificationData
     */
    public function frontendTagVerification(array $verificationData)
    {
        $tagName = (isset($verificationData['new_tag_names'])) ? $verificationData['new_tag_names'] : NULL;
        $productName = (isset($verificationData['product_name'])) ? $verificationData['product_name'] : NULL;
        if ($tagName && $productName) {
            //Verification in "My Recent tags" area
            $this->navigate('customer_account');
            $this->addParameter('productName', $productName);
            $tagNameArray = $this->convertStringToArray($tagName);
            foreach ($tagNameArray as $value) {
                $this->addParameter('tagName', $value);
                $this->assertTrue($this->controlIsPresent('link', 'product_info'), "Cannot find tag with name: $value");
                $this->clickControl('link', 'product_info');
                $this->assertTrue($this->controlIsPresent('link', 'product_name'), "Cannot find tag with name: $value");
                $this->assertTrue($this->controlIsPresent('pageelement', 'tag_name_box'),
                        "Cannot find tag with name: $value");
                $this->navigate('customer_account');
            }
            //Verification in "My Account -> My Tags"
            $this->navigate('my_account_my_tags');
            foreach ($tagNameArray as $value) {
                $this->addParameter('tagName', trim($value, " \x22\x27"));
                $this->assertTrue($this->controlIsPresent('link', 'tag_name'), "Cannot find tag with name: $value");
                $this->clickControl('link', 'tag_name');
                $this->assertTrue($this->controlIsPresent('link', 'product_name'), "Cannot find tag with name: $value");
                $this->assertTrue($this->controlIsPresent('pageelement', 'tag_name_box'),
                        "Cannot find tag with name: $value");
                $this->clickControl('link', 'back_to_tags_list');
            }
        } else {
            $this->fail('Verification Data is not correct');
        }
    }

    /**
     * Verification tags in category
     *
     * @param array $verificationData
     */
    public function frontendTagVerificationInCategory(array $verificationData)
    {
        $category = (isset($verificationData['category'])) ? $verificationData['category'] : NULL;
        $productName = (isset($verificationData['product_name'])) ? $verificationData['product_name'] : NULL;
        $tagName = (isset($verificationData['new_tag_names'])) ? $verificationData['new_tag_names'] : NULL;
        if ($category && $productName && $tagName) {
            $this->addParameter('productName', $verificationData['product_name']);
            $tagNameArray = $this->convertStringToArray($tagName);
            $category = substr($category, strpos($category, '/') + 1);
            $url = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '-', $category)), '-');
            $this->addParameter('categoryTitle', $category);
            $this->addParameter('categoryUrl', $url);
            foreach ($tagNameArray as $value) {
                $this->frontend('category_page');
                $this->addParameter('tagName', $value);
                $this->assertTrue($this->controlIsPresent('link', 'tag_name'), "Cannot find tag with name: $value");
                $this->clickControl('link', 'tag_name');
                $this->addParameter('id', $this->defineIdFromUrl());
                $this->assertTrue($this->controlIsPresent('link', 'product_name'));
            }
        } else {
            $this->fail('Verification Data is not correct');
        }
    }

    /**
     * Delete tag
     *
     * @param array $verificationData
     */
    public function frontendDeleteTag(array $verificationData)
    {
        $tagName = (isset($verificationData['new_tag_names'])) ? $verificationData['new_tag_names'] : NULL;
        if ($tagName) {
            $tagNameArray = $this->convertStringToArray($tagName);
            foreach ($tagNameArray as $value) {
                $this->addParameter('tagName', $value);
                $this->assertTrue($this->controlIsPresent('link', 'tag_name'), "Cannot find tag with name: $value");
                $this->clickControl('link', 'tag_name');
                $this->clickButtonAndConfirm('delete_tag', 'confirmation_for_delete');
                $this->addParameter('id', $this->defineIdFromUrl());
                $this->assertTrue($this->successMessage('success_deleted_tag'), $this->messages);
            }
        } else {
            $this->fail('Verification Data is not correct');
        }
    }

    /* ----------------------------------- Backend ----------------------------------- */

    /**
     * Select store view on Create/Edit tag page
     *
     * @param string $store_view_name Name of the store
     */
    protected function selectStoreView($store_view_name)
    {
        $xpath = $this->_getControlXpath('dropdown', 'switch_store');
        $toSelect = $xpath . "//option[contains(.,'" . $store_view_name . "')]";
        $isSelected = $toSelect . '[@selected]';
        if (!$this->isElementPresent($isSelected)) {
            $storeId = $this->getAttribute($toSelect . '/@value');
            $this->addParameter('storeId', $storeId);
            $this->fillForm(array('switch_store' => $store_view_name));
            $this->waitForPageToLoad();
        }
    }

    /**
     * Edits a tag in backend
     *
     * @param string|array $tagData
     */
    public function fillTagSettings($tagData)
    {
        if (is_string($tagData))
            $tagData = $this->loadData($tagData);
        $tagData = $this->arrayEmptyClear($tagData);
        // Select store view if available
        if (array_key_exists('switch_store', $tagData)) {
            if ($this->controlIsPresent('dropdown', 'switch_store')) {
                $this->selectStoreView($tagData['switch_store']);
            } else {
                unset($tagData['switch_store']);
            }
        }
        $prod_tag_admin = (isset($tagData['products_tagged_by_admins'])) ? $tagData['products_tagged_by_admins'] : null;
        // Fill general options
        $this->fillForm($tagData, 'general_info');
        if ($prod_tag_admin) {
            // Add tag name to parameters
            $tagName = $this->getValue($this->_getControlXpath('field', 'tag_name'));
            $this->addParameter('tagName', $tagName);
            //Fill additional options
            $this->clickButton('save_and_continue_edit');
            if (!$this->controlIsPresent('field', 'prod_tag_admin_name')) {
                $this->clickControl('link', 'prod_tag_admin_expand', false);
                $this->waitForAjax();
            }
            $this->searchAndChoose($prod_tag_admin, 'products_tagged_by_admins');
        }
    }

    /**
     * Adds a new tag in backend
     *
     * @param string|array $tagData
     */
    public function addTag($tagData)
    {
        $this->clickButton('add_new_tag');
        $this->fillTagSettings($tagData);
        $this->saveForm('save_tag');
    }

    /**
     * Opens a tag in backend
     *
     * @param string|array $searchData Data used in Search Grid for tags
     */
    public function openTag($searchData)
    {
        if (is_string($searchData))
            $searchData = $this->loadData($searchData);
        $searchData = $this->arrayEmptyClear($searchData);
        // Check if store views are available
        $key = 'filter_store_view';
        if (array_key_exists($key, $searchData) && !$this->controlIsPresent('dropdown', 'store_view')) {
            unset($searchData[$key]);
        }
        // Search and open
        $xpathTR = $this->search($searchData, 'tags_grid');
        $this->assertNotEquals(null, $xpathTR, 'Tag ' . implode(',', $searchData) . ' is not found');
        $names = $this->shoppingCartHelper()->getColumnNamesAndNumbers('tags_grid_head', false);
        if (array_key_exists('Tag', $names)) {
            $text = $this->getText($xpathTR . '//td[' . $names['Tag'] . ']');
            $this->addParameter('tagName', $text);
        }
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->click($xpathTR . '//td[' . $names['Tag'] . ']');
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage();
    }

    /**
     * Mass action: approves a set of tags in backend
     *
     * @param array $tagsSearchData Array of tags to change status
     * @param string $newStatus New status, e.g. 'Approved'
     *
     * Example of $tagsSearchData for one tag with 'my tag name' name: array(array('tag_name' => 'my tag name'))
     */
    public function changeTagsStatus(array $tagsSearchData, $newStatus)
    {
        foreach ($tagsSearchData as $searchData) {
            $this->searchAndChoose($searchData);
        }
        $this->fillForm(array('tags_massaction' => 'Change status', 'tags_status' => $newStatus));
        $this->clickButton('submit');
    }

    /**
     * Deletes a tag from backend
     *
     * @param string|array $searchData Data used in Search Grid for tags. Same as data used for openTag
     */
    public function deleteTag($searchData)
    {
        $this->openTag($searchData);
        $this->clickButtonAndConfirm('delete_tag', 'confirmation_for_delete');
    }

    /**
     * Checks if the tag is assigned to the product.
     * Returns true if assigned, or False otherwise.
     *
     * @param array $tagSearchData Data used in Search Grid for tags. Same as used for openTag
     * @param array $productSearchData Product to open. Same as used in productHelper()->openProduct
     */
    public function verifyTagProduct(array $tagSearchData, array $productSearchData)
    {
        $this->productHelper()->openProduct($productSearchData);
        $this->clickControl('tab', 'product_tags', false);
        $this->pleaseWait();
        $xpathTR = $this->search($tagSearchData, 'product_tags');
        return $xpathTR ? true : false;
    }

    /**
     * Checks if the customer submmitted the tag.
     * Returns true if submitted, or False otherwise.
     *
     * @param array $tagSearchData Data used in Search Grid for tags. Same as data used for openTag
     * @param array $customerSearchData Search data to open customer. Same as in customerHelper()->openCustomer
     */
    public function verifyTagCustomer(array $tagSearchData, array $customerSearchData)
    {
        $tagSearchData = $this->arrayEmptyClear($tagSearchData);
        $this->customerHelper()->openCustomer($customerSearchData);
        $this->clickControl('tab', 'product_tags', false);
        $this->pleaseWait();
        $xpathTR = $this->formSearchXpath($tagSearchData);
        do {
            if ($this->isElementPresent($xpathTR))
                return true;
            if ($this->controlIsPresent('link', 'next_page')) {
                $this->clickControl('link', 'next_page', false);
                $this->pleaseWait();
            } else
                break;
        } while (true);

        return false;
    }

}
