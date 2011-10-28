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
     * @param string $tagName
     */
    public function frontendAddTag($tagName)
    {
        if (is_array($tagName) && array_key_exists('new_tag_names', $tagName)) {
            $tagName = $tagName['new_tag_names'];
        } else {
            $this->fail('Array key is absent in array');
        }
        $tagQty = count(explode(' ', $tagName));
        $this->addParameter('tagQty', $tagQty);
        $tagXpath = $this->_getControlXpath('field', 'input_new_tags');
        if (!$this->isElementPresent($tagXpath)) {
            $this->fail('Element is absent on the page');
        }
        $this->type($tagXpath, $tagName);
        $this->clickButton('add_tags');
        if ($this->checkCurrentPage('customer_login') == TRUE) {
            $this->fail('Customer is not Logged In');
        }
    }

    /**
     * Verification tags on frontend
     *
     * @param array $verificationData
     */
    public function frontendTagVerification($verificationData)
    {
        if (is_array($verificationData) && array_key_exists('new_tag_names', $verificationData)) {
            $tagName = $verificationData['new_tag_names'];
        } else {
            $this->fail('Array key is absent in array');
        }
        if (array_key_exists('product_name', $verificationData)) {
            $productName = $verificationData['product_name'];
        } else {
            $this->fail('Array key is absent in array');
        }
        //Verification in "My Recent tags" area
        $this->navigate('customer_account');
        $this->addParameter('productName', $productName);
        $tagNameArray = array();
        preg_match_all('/[^\s\']+/', $tagName, $tagNameArray);
        foreach ($tagNameArray[0] as $value) {
            $this->addParameter('tagName', $value);
            $tagXpath = $this->_getControlXpath('link', 'product_info');
            $this->assertTrue($this->isElementPresent($tagXpath), "Cannot find tag with name: $value");
            $this->clickControl('link', 'product_info');
            $tagXpath = $this->_getControlXpath('link', 'product_name');
            $this->assertTrue($this->isElementPresent($tagXpath), "Cannot find tag with name: $value");
            $tagXpath = $this->_getControlXpath('pageelement', 'tag_name_box');
            $this->assertTrue($this->isElementPresent($tagXpath), "Cannot find tag with name: $value");
            $this->navigate('customer_account');
        }
        //Verification in "My Account -> My Tags"
        $this->navigate('my_account_my_tags');
        foreach ($tagNameArray[0] as $value) {
            $this->addParameter('tagName', $value);
            $tagXpath = $this->_getControlXpath('link', 'tag_name');
            $this->assertTrue($this->isElementPresent($tagXpath), "Cannot find tag with name: $value");
            $this->clickControl('link', 'tag_name');
            $tagXpath = $this->_getControlXpath('link', 'product_name');
            $this->assertTrue($this->isElementPresent($tagXpath), "Cannot find tag with name: $value");
            $tagXpath = $this->_getControlXpath('pageelement', 'tag_name_box');
            $this->assertTrue($this->isElementPresent($tagXpath), "Cannot find tag with name: $value");
            $this->clickControl('link', 'back_to_tags_list');
        }
    }

    /**
     * Verification tags in category
     *
     * @param array $verificationData
     */
    public function frontendTagVerificationInCategory($verificationData)
    {
        if (is_array($verificationData) && array_key_exists('new_tag_names', $verificationData)) {
            $tagName = $verificationData['new_tag_names'];
        } else {
            $this->fail('Array keys are absent in array');
        }
        $category = $verificationData['category'];
        $category = substr($category, strpos($category, '/') + 1);
        $url = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '-', $category)), '-');
        $this->addParameter('categoryTitle', $category);
        $this->addParameter('categoryUrl', $url);
        $this->frontend('category_page');
        $tagNameArray = array();
        preg_match_all('/[^\s\']+/', $tagName, $tagNameArray);
        foreach ($tagNameArray[0] as $value) {
            $this->addParameter('tagName', $value);
            $tagXpath = $this->_getControlXpath('link', 'tag_name');
            $this->assertTrue($this->isElementPresent($tagXpath), "Cannot find tag with name: $value");
            $this->clickControl('link', 'tag_name');
        }
    }

    /**
     * Approve Tags
     *
     * @param array $verificationData
     */
    public function backendApproveTags($verificationData)
    {
        if (is_array($verificationData) && array_key_exists('new_tag_names', $verificationData)) {
            $tagName = $verificationData['new_tag_names'];
        } else {
            $this->fail('Array keys are absent in array');
        }
        $tagNameArray = array();
        preg_match_all('/[^\s\']+/', $tagName, $tagNameArray);
        foreach ($tagNameArray[0] as $value) {
            $this->addParameter('tagName', $value);
            $tagXpath = $this->_getControlXpath('link', 'tag_name');
            $this->assertTrue($this->isElementPresent($tagXpath), "Cannot find tag with name: $value");
            $this->clickControl('link', 'tag_name');
        }
    }

    /**
     * Delete tag
     * Need to be modified
     *
     * @param array $verificationData
     */
    public function frontendDeleteTag($verificationData)
    {
        if (is_array($verificationData) && array_key_exists('new_tag_names', $verificationData)) {
            $tagName = $verificationData['new_tag_names'];
        } else {
            $this->fail('Array key is absent in array');
        }
        $tagNameArray = explode(' ', $tagName);
//        print_r($tagNameArray);

        foreach ($tagNameArray as $value) {
            $this->addParameter('tagName', $value);
            $xpath = $this->_getControlXpath('link', 'tag_name');
            $this->assertTrue($this->isElementPresent($xpath), "Cannot find tag with name: $value");
            $this->clickControl('link', 'tag_name');
            $this->clickControl('link', 'delete_tag');
            $this->pleaseWait();
            $this->answerOnNextPrompt('OK');
            $this->pleaseWait();
            $this->assertTrue($this->successMessage('success_deleted_tag'), $this->messages);
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
        if (!$store_view_name) {
            return false;
        }
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
                $store_view = (isset($tagData['switch_store'])) ? $tagData['switch_store'] : NULL;
                $this->selectStoreView($store_view);
            } else {
                unset($tagData['switch_store']);
            }
        }
        // Fill general options
        $this->fillForm($tagData, 'general_info');
        // Add tag name to parameters
        $xpathTagName = $this->_getControlXpath('field', 'tag_name');
        $tagName = $this->getElementByXpath($xpathTagName, 'value');
        if (empty($tagName)) {
            print_r('Tag name is empty!');
        } else {
            $this->addParameter('tagName', $tagName);
        }
        //Fill additional options
        $this->clickButton('save_and_continue_edit');
        if (!$this->controlIsPresent('field', 'prod_tag_admin_name')) {
            $this->clickControl('link', 'prod_tag_admin_expand', false);
            $this->waitForAjax();
        }
        $prod_tag_admin = (isset($tagData['products_tagged_by_admins'])) ? $tagData['products_tagged_by_admins'] : null;
        if ($prod_tag_admin) {
            $this->searchAndChoose($prod_tag_admin, 'products_tagged_by_admins');
        };
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
        $this->clickButton('save_tag');
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
        if (array_key_exists($key, $searchData)) {
            if (!$this->controlIsPresent('dropdown', 'store_view')) {
                unset($searchData[$key]);
            }
        }
        // Search and open
        $this->navigate('all_tags');
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
     * Approves a tag in backend
     *
     * @param string|array $searchData Data used in Search Grid for tags. Same as data used for openTag
     * @param string $newStatus New status
     */
    public function changeTagStatus($searchData, $newStatus)
    {
        $this->openTag($searchData);
        $this->fillTagSettings(array('tag_status' => $newStatus));
        $this->clickButton('save_tag');
    }

    /**
     * Mass action: approves tags in backend
     *
     * @param array $tagsSearchData Set of tags to change status
     * @param string $newStatus New status
     */
    public function changeTagsStatus(array $tagsSearchData, $newStatus)
    {
        foreach ($tagsSearchData as $searchData) {
            $this->searchAndChoose($searchData);
        }
        $this->fillForm(array('tags_massaction' => 'Change status'));
        $this->fillForm(array('tags_status' => $newStatus));
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
     * Verify a tag from backend for product
     *
     * @param array $searchData Data used in Search Grid for tags. Same as data used for openTag
     * @param string $productName Product Name to verify tag
     */
    public function verifyTagProduct($searchData,$productName)
    {
        $this->productHelper()->openProduct(array('product_name' => $productName));
        $this->clickControl('tab', 'product_tags', false);
        $this->pleaseWait();
        $xpathTR = $this->search($searchData, 'product_tags');
        return $xpathTR ? true : false ;
    }

     /**
     * Verify a tag from backend for customer
     *
     * @param array $searchData Data used in Search Grid for tags. Same as data used for openTag
     * @param array $searchCustomer
     */
    public function verifyTagCustomer($searchTag,$searchCustomer)
    {
        $searchTag = $this->arrayEmptyClear($searchTag);
        $this->customerHelper()->openCustomer($searchCustomer);
        $this->clickControl('tab', 'product_tags', false);
        $this->pleaseWait();
        $xpathTR = $this->formSearchXpath($searchTag);
        do{
            if ($this->isElementPresent($xpathTR))
                return true;
            if ($this->controlIsPresent('link', 'next_page')){
                $this->clickControl('link', 'next_page',false);
                $this->pleaseWait();
            }else
                break;
        } while (true);

        return false;
    }
}
