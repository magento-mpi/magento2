<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tags
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
class Core_Mage_Tags_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Converts string with tags to an array for verification
     *
     * @param string $tagName
     *
     * @return array
     */
    public function _convertTagsStringToArray($tagName)
    {
        $tags = array();
        $tagNameArray = array_filter(explode("\n", preg_replace("/(\'(.*?)\')|(\s+)/i", "$1\n", $tagName)), 'strlen');
        foreach ($tagNameArray as $key => $value) {
            $tags[$key] = trim($value, " \x22\x27");
            $tags[$key] = htmlspecialchars($tags[$key]);
        }
        return $tags;
    }

    /**
     * <p>Create Tag</p>
     *
     * @param string $tagsString Tags to add
     */
    public function frontendAddTag($tagsString)
    {
        $tagNameArray = $this->_convertTagsStringToArray($tagsString);
        $tagQty = count($tagNameArray);
        $this->addParameter('tagQty', $tagQty);
        $this->fillField('input_new_tags', $tagsString);
        $this->clickButton('add_tags');
    }

    /**
     * Delete tag
     *
     * @param string|array $tags
     */
    public function frontendDeleteTags($tags)
    {
        if (is_string($tags)) {
            $tags = $this->_convertTagsStringToArray($tags);
        }
        foreach ($tags as $tag) {
            $this->addParameter('tagName', $tag);
            $this->clickControl('link', 'tag_name');
            $this->clickButtonAndConfirm('delete_tag', 'confirmation_for_delete', false);
            $this->waitForPageToLoad();
            $this->addParameter('uenc', $this->defineParameterFromUrl('uenc'));
            $this->validatePage('my_account_my_tags_after_delete');
        }
    }

    /**
     * Verification tags on frontend
     *
     * @param string|array $tags
     * @param string $product
     */
    public function frontendTagVerification($tags, $product)
    {
        if (is_string($tags)) {
            $tags = $this->_convertTagsStringToArray($tags);
        }
        //Verification in "My Recent tags" area
        $this->addParameter('productName', $product);
        foreach ($tags as $tag) {
            $this->navigate('customer_account');
            $this->addParameter('tagName', $tag);
            $this->assertTrue($this->controlIsPresent('link', 'tag'), "Cannot find tag with name: $tag");
            $this->clickControl('link', 'tag');
            $this->assertTrue($this->controlIsPresent('pageelement', 'tag_name_box'),
                "Cannot find tag $tag in My Tags");
            $this->assertTrue($this->controlIsPresent('link', 'product_name'),
                "Cannot find product $product tagged with $tag");
        }
        //Verification in "My Account -> My Tags"
        foreach ($tags as $tag) {
            $this->navigate('my_account_my_tags');
            $this->addParameter('tagName', $tag);
            $this->assertTrue($this->controlIsPresent('link', 'tag_name'), "Cannot find tag with name: $tag");
            $this->clickControl('link', 'tag_name');
            $this->assertTrue($this->controlIsPresent('pageelement', 'tag_name_box'),
                "Cannot find tag $tag in My Tags");
            $this->assertTrue($this->controlIsPresent('link', 'product_name'),
                "Cannot find product $product tagged with $tag");
        }
    }

    /**
     * Verification tags in category
     *
     * @param string|array $tags
     * @param string $product
     * @param string $category
     */
    public function frontendTagVerificationInCategory($tags, $product, $category)
    {
        if (is_string($tags)) {
            $tags = $this->_convertTagsStringToArray($tags);
        }
        $category = substr($category, strpos($category, '/') + 1);
        $url = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '-', $category)), '-');
        $this->addParameter('productName', $product);
        $this->addParameter('elementTitle', $category);
        $this->addParameter('categoryUrl', $url);
        foreach ($tags as $tag) {
            $this->frontend('category_page_before_reindex');
            $this->addParameter('tagName', $tag);
            $this->assertTrue($this->controlIsPresent('link', 'tag_name'), "Cannot find tag with name: $tag");
            $this->addParameter('elementTitle', $tag);
            $this->clickControl('link', 'tag_name');
            $this->assertTrue($this->checkCurrentPage('tags_products'), $this->getParsedMessages());
            $this->assertTrue($this->controlIsPresent('link', 'product_name'),
                'Product with name "' . $product . '" is not tagged with "' . $tag . '" tag');
        }
    }

    /* ----------------------------------- Backend ----------------------------------- */
    /**
     * Edits a tag in backend
     *
     * @param string|array $tagData
     */
    public function fillTagSettings($tagData)
    {
        $tagData = $this->fixtureDataToArray($tagData);
        // Select store view if available
        if (array_key_exists('choose_store_view', $tagData)) {
            if ($this->controlIsPresent('dropdown', 'choose_store_view')) {
                $this->selectStoreScope('dropdown', 'choose_store_view', $tagData['choose_store_view']);
            } else {
                unset($tagData['choose_store_view']);
            }
        }
        $prodTagAdmin =
            (isset($tagData['products_tagged_by_admins'])) ? $tagData['products_tagged_by_admins'] : array();
        // Fill general options
        $this->fillForm($tagData);
        if ($prodTagAdmin) {
            // Add tag name to parameters
            $tagName = $this->getControlAttribute('field', 'tag_name', 'value');
            $this->addParameter('elementTitle', 'Edit Tag ' . '\'' . $tagName . '\'');
            //Fill additional options
            $this->clickButton('save_and_continue_edit');
            $this->clickButton('reset');
            if (!$this->controlIsPresent('field', 'prod_tag_admin_name')) {
                $this->clickControl('link', 'prod_tag_admin_expand', false);
                $this->pleaseWait();
            }
            $this->searchAndChoose($prodTagAdmin, 'products_tagged_by_admins');
        }
    }

    /**
     * Adds a new tag in backend
     *
     * @param string|array $tagData
     */
    public function addTag($tagData)
    {
        $this->addParameter('storeId', '1');
        $this->clickButton('add_new_tag');
        $this->fillTagSettings($tagData);
        $this->saveForm('save_tag');
    }

    /**
     * Opens a tag in backend
     *
     * @param string|array $searchData Data used in Search Grid for tags
     * @param string $fieldsetName
     */
    public function openTag($searchData, $fieldsetName = 'tags_grid')
    {
        $searchData = $this->fixtureDataToArray($searchData);
        if (isset($searchData['filter_store_view']) && !$this->controlIsVisible('dropdown', 'filter_store_view')) {
            unset($searchData['filter_store_view']);
        }
        //Search Tag
        $searchData = $this->_prepareDataForSearch($searchData);
        $tagLocator = $this->search($searchData, $fieldsetName);
        $this->assertNotNull($tagLocator, 'Tag is not found with data: ' . print_r($searchData, true));
        $tagRowElement = $this->getElement($tagLocator);
        $tagUrl = $tagRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Tag', $this->_getControlXpath('fieldset', $fieldsetName));
        $cellElement = $this->getChildElement($tagRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', "Edit Tag '" . trim($cellElement->text()) . "'");
        $this->addParameter('id', $this->defineIdFromUrl($tagUrl));
        //Open Tag
        $this->url($tagUrl);
        $this->addParameter('storeId', $this->defineParameterFromUrl('store'));
        $this->validatePage();
    }

    /**
     * Mass action: changes tags status in backend
     *
     * @param array $tagsSearchData Array of tags to change status
     * @param string $newStatus New status, e.g. 'Approved'
     *
     * Example of $tagsSearchData for one tag with 'my tag name' name: array(array('tag_name' => 'my tag name'))
     */
    public function changeTagsStatus(array $tagsSearchData, $newStatus)
    {
        foreach ($tagsSearchData as $searchData) {
            $this->searchAndChoose($searchData, 'tags_grid');
        }
        $this->fillDropdown('tags_massaction', 'Change status');
        $this->fillDropdown('tags_status', $newStatus);
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
     * Delete all tags
     * @return bool
     */
    public function deleteAllTags()
    {
        if ($this->controlIsPresent('message', 'no_records_found')) {
            return true;
        }
        $this->clickControl('link', 'select_all', false);
        $this->fillDropdown('tags_massaction', 'Delete');
        $waitCondition = $this->getBasicXpathMessagesExcludeCurrent(array('success', 'error', 'validation'));
        $this->clickButtonAndConfirm('submit', 'confirmation_for_massaction_delete', false);
        $this->waitForElement($waitCondition);
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->validatePage();
        $this->assertMessagePresent('success');
        return true;
    }

    /**
     * Checks if the tag is assigned to the product.
     * Returns true if assigned, or False otherwise.
     *
     * @param array $tagSearchData Data used in Search Grid for tags. Same as used for openTag
     * @param array $productSearchData Product to open. Same as used in productHelper()->openProduct
     *
     * @return bool
     */
    public function verifyTagProduct(array $tagSearchData, array $productSearchData)
    {
        $this->productHelper()->openProduct($productSearchData);
        $this->productHelper()->openProductTab('product_tags');
        $xpathTR = $this->search($tagSearchData, 'product_tags');
        return $xpathTR ? true : false;
    }

    /**
     * Checks if the customer submitted the tag.
     * Returns true if submitted, or False otherwise.
     *
     * @param array $tagSearchData Data used in Search Grid for tags. Same as data used for openTag
     * @param array $customerSearchData Search data to open customer. Same as in customerHelper()->openCustomer
     *
     * @return bool
     */
    public function verifyTagCustomer(array $tagSearchData, array $customerSearchData)
    {
        $this->customerHelper()->openCustomer($customerSearchData);
        $this->openTab('product_tags');
        $xpathTR = $this->formSearchXpath($tagSearchData);
        $this->addParameter('cellIndex', 1);
        $this->addParameter('tableLineXpath', $xpathTR);
        do {
            if ($this->controlIsPresent('pageelement', 'table_line_cell_index')) {
                return true;
            }
            if ($this->controlIsPresent('link', 'next_page')) {
                $this->clickControl('link', 'next_page', false);
                $this->pleaseWait();
            } else {
                break;
            }
        } while (true);

        return false;
    }

    /**
     * Checks tag.
     *
     * @param array $tagData Data used in Search Grid for tags. Same as used for openTag
     * @param array|null $products
     * @param array|null $customers
     *
     * @return bool
     */
    public function verifyTag(array $tagData, array $products = null, array $customers = null)
    {
        $this->openTag($tagData);
        $this->assertTrue($this->verifyForm($tagData), 'Tag verification is failure ' . print_r($tagData, true));
        //Verification in "Edit Tag" -> "Customers Submitted this Tag", "Products Tagged by Customers"
        $this->clickControl('link', 'prod_tag_customer_expand', false);
        $this->waitForAjax();
        if ($products) {
            foreach ($products as $product) {
                $this->assertNotNull($this->search(array('prod_tag_customer_product_name' => $product['name']),
                        'products_tagged_by_customers'),
                    "Cannot find product $product in Products Tagged by Customers");
            }
        }
        $this->clickControl('link', 'customers_submit_tag_expand', false);
        $this->waitForAjax();
        if ($customers) {
            foreach ($customers as $customer) {
                $this->assertNotNull($this->search(
                    array(
                        'first_name' => $customer['first_name'],
                        'last_name' => $customer['last_name']
                    ),
                    'customers_submitted_tags'
                ), "Cannot find customer in Customers Submitted this Tag");
            }
        }
        return true;
    }

    /**
     * Checks if the customer tagged product is assigned to the product.
     * Returns true if assigned, or False otherwise.
     *
     * @param array $tagSearchData Data used in Search Grid for tags. Same as used for openTag
     * @param array $productSearchData Product to open. Same as used in productHelper()->openProduct
     *
     * @return bool
     */
    public function verifyCustomerTaggedProduct(array $tagSearchData, array $productSearchData)
    {
        $this->productHelper()->openProduct($productSearchData);
        $this->productHelper()->openProductTab('customer_tags');
        $xpathTR = $this->search($tagSearchData, 'customer_tags');
        return $xpathTR ? true : false;
    }

    /**
     * Checks if tag is selected in grid.
     * Returns true if selected, or false otherwise.
     *
     * @param array $tagSearchData Data used in Search Grid for tags. Same as data used for openTag
     *
     * @return bool
     */
    public function isTagSelected(array $tagSearchData)
    {
        $this->_prepareDataForSearch($tagSearchData);
        $xpathTR = $this->search($tagSearchData, 'tags_grid');
        $this->assertNotNull($xpathTR, 'Tag is not found');
        $xpathTR .= "//input[contains(@class,'checkbox') or contains(@class,'radio')][not(@disabled)]";
        return $this->getElement($xpathTR)->selected();
    }
}