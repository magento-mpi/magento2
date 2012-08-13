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
class Core_Mage_Tags_Helper extends Mage_Selenium_TestCase
{
    /**
     * Converts string with tags to an array for verification
     *
     * @param string $tagName
     *
     * @return array
     */
    public function convertTagsStringToArray($tagName)
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
        $tagNameArray = $this->convertTagsStringToArray($tagsString);
        $tagQty = count($tagNameArray);
        $this->addParameter('tagQty', $tagQty);
        $this->addParameter('tagName', $tagsString);
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
            $tags = $this->convertTagsStringToArray($tags);
        }
        foreach ($tags as $tag) {
            $this->addParameter('tagName', $tag);
            $this->clickControl('link', 'tag_name');
            $this->clickButtonAndConfirm('delete_tag', 'confirmation_for_delete', false);
            $this->waitForPageToLoad($this->_browserTimeoutPeriod);
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
    public function frontendTagVerification($tags, $product )
    {
        if (is_string($tags)) {
            $tags = $this->convertTagsStringToArray($tags);
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
            $tags = $this->convertTagsStringToArray($tags);
        }
        $category = substr($category, strpos($category, '/') + 1);
        $url = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '-', $category)), '-');
        $this->addParameter('productName', $product);
        $this->addParameter('categoryTitle', $category);
        $this->addParameter('categoryUrl', $url);
        foreach ($tags as $tag) {
            $this->frontend('category_page_before_reindex');
            $this->addParameter('tagName', $tag);
            $this->assertTrue($this->controlIsPresent('link', 'tag_name'), "Cannot find tag with name: $tag");
            $this->clickControl('link', 'tag_name');
            $this->assertTrue($this->checkCurrentPage('tags_products'), $this->getParsedMessages());
            $this->assertTrue($this->controlIsPresent('link', 'product_name'));
        }
    }

    /* ----------------------------------- Backend ----------------------------------- */

    /**
     * Select store view on Create/Edit tag page
     *
     * @param string $storeViewName Name of the store
     */
    protected function selectStoreView($storeViewName)
    {
        $xpath = $this->_getControlXpath('dropdown', 'switch_store');
        $toSelect = $xpath . "//option[contains(.,'" . $storeViewName . "')]";
        $isSelected = $toSelect . '[@selected]';
        if (!$this->isElementPresent($isSelected)) {
            $storeId = $this->getAttribute($toSelect . '/@value');
            $this->addParameter('storeId', $storeId);
            $this->fillDropdown('switch_store', $storeViewName);
            $this->waitForPageToLoad($this->_browserTimeoutPeriod);
            $this->validatePage();
        }
    }

    /**
     * Edits a tag in backend
     *
     * @param string|array $tagData
     */
    public function fillTagSettings($tagData)
    {
        // Select store view if available
        if (array_key_exists('switch_store', $tagData)) {
            if ($this->controlIsPresent('dropdown', 'switch_store')) {
                $this->selectStoreView($tagData['switch_store']);
            } else {
                unset($tagData['switch_store']);
            }
        }
        $prodTagAdmin =
            (isset($tagData['products_tagged_by_admins'])) ? $tagData['products_tagged_by_admins'] : array();
        // Fill general options
        $this->fillForm($tagData);
        if ($prodTagAdmin) {
            // Add tag name to parameters
            $tagName = $this->getValue($this->_getControlXpath('field', 'tag_name'));
            $this->addParameter('tagName', $tagName);
            //Fill additional options
            $this->clickButton('save_and_continue_edit');
            if (!$this->controlIsPresent('field', 'prod_tag_admin_name')) {
                $this->clickControl('link', 'prod_tag_admin_expand', false);
                $this->waitForAjax();
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
     */
    public function openTag($searchData)
    {
        // Check if store views are available
        $key = 'filter_store_view';
        if (array_key_exists($key, $searchData) && !$this->controlIsPresent('dropdown', 'store_view')) {
            unset($searchData[$key]);
        }
        // Search and open
        $xpathTR = $this->search($searchData);
        $this->assertNotNull($xpathTR, 'Tag is not found');
        //get Table xPath depends page content
        $tableXpath = $this->_getControlXpath('pageelement', 'tags_grid');
        $cellId = $this->getColumnIdByName('Tag', $tableXpath);
        $this->addParameter('tagName', $this->getText($xpathTR . '//td[' . $cellId . ']'));
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->click($xpathTR . '//td[' . $cellId . ']');
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->addParameter('prodId', $this->defineParameterFromUrl('product_id'));
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
     * Delete all tags
     * @return bool
     */
    public function deleteAllTags()
    {
        if ($this->controlIsPresent('message', 'no_records_found')) {
            return true;
        }
        $this->clickControl('link', 'select_all', false);
        $this->waitForAjax();
        $this->fillDropdown('tags_massaction', 'Delete');
        $this->_parseMessages();
        foreach (self::$_messages as $key => $value) {
            self::$_messages[$key] = array_unique($value);
        }
        $success = $this->_getMessageXpath('general_success');
        $error = $this->_getMessageXpath('general_error');
        $validation = $this->_getMessageXpath('general_validation');
        $types = array('success', 'error', 'validation');
        foreach ($types as $message) {
            if (array_key_exists($message, self::$_messages)) {
                $exclude = '';
                foreach (self::$_messages[$message] as $messageText) {
                    $exclude .= "[not(..//.='$messageText')]";
                }
                ${$message} .= $exclude;
            }
        }
        $this->clickButtonAndConfirm('submit', 'confirmation_for_massaction_delete', false);
        $this->waitForElement(array($success, $error, $validation));
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->validatePage();
        $this->assertMessagePresent('success');
        return true;
    }

    /**
     * Checks tag.
     *
     * @param array $tagData Data used in Search Grid for tags. Same as used for openTag
     * @param array|null $products
     * @param array|null $customers
     * @return bool
     */
    public function verifyTag(array $tagData, array $products = null, array $customers = null)
    {
        $this->openTag($tagData);
        $this->assertTrue($this->verifyForm($tagData),
        'Tag verification is failure ' . print_r($tagData, true));
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
                $this->assertNotNull($this->search(array('first_name' => $customer['first_name'],
                        'last_name' => $customer['last_name']), 'customers_submitted_tags'),
                     "Cannot find customer in Customers Submitted this Tag");
            }
        }
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
        $this->openTab('product_tags');
        $xpathTR = $this->search($tagSearchData, 'product_tags');
        return $xpathTR ? true : false;
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
        $this->openTab('customer_tags');
        $xpathTR = $this->search($tagSearchData, 'customer_tags');
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
        do {
            if ($this->isElementPresent($xpathTR)) {
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
        $xpathTR = $this->search($tagSearchData);
        if ($xpathTR) {
            $xpathTR .= "//input[contains(@class,'checkbox') or contains(@class,'radio')][not(@disabled)]";
            if ($this->getValue($xpathTR) != 'off') {
                return true;
            }
        }
        return false;
    }

    /**
     * Searches the specified data in the tags report.
     * Returns row number(s) if found, or null otherwise.
     *
     * @param array $data Data to look for in report
     *
     * @return string|array|null
     */
    public function searchDataInReport(array $data, $fieldSetName = null)
    {
        $rowNumbers = array();
        $qtyElementsInTable = $this->_getControlXpath('pageelement', 'qtyElementsInTable');
        $totalCount = intval($this->getText($qtyElementsInTable));
        $xpathTR = $this->formSearchXpath($data);
        if ($this->isElementPresent($xpathTR)) {
            for ($i = 1; $i <= $totalCount; $i++) {
                if ($this->isElementPresent(str_replace('tr', 'tr[' . $i .']', $xpathTR))) {
                    $rowNumbers[] = $i;
                }
            }
            if (count($rowNumbers) == 1) {
                return $rowNumbers[0];
            } else {
                return $rowNumbers;
            }
        }
        return null;
    }

    /**
     * Verifies if report sorting by specific column is correct
     *
     * @param array $data Data to look for in report
     * @param string $column Column that report is sorted by
     *
     * @return void
     */
    public function verifyReportSortingByColumn(array $data, $column)
    {
        $currentSorting = array();
        $newSorting = array();
        for ($i = 0; $i < count($data); $i++) {
            $currentSorting[$i] = $data[$i][$column];
            $newSorting[$i] = $data[$i][$column];
        }
        sort($newSorting);
        $sortedReport = array();
        for ($i = 0; $i < count($data); $i++) {
            for ($j = 0; $j < count($data); $j++) {
                if ($currentSorting[$i] && $newSorting[$j] && $currentSorting[$i] == $newSorting[$j]) {
                    $sortedReport[$j] = $data[$i];
                    unset($currentSorting[$i]);
                    unset($newSorting[$j]);
                }
            }
        }
        for ($i = 0; $i < count($sortedReport); $i++) {
            $this->assertEquals($i+1, $this->tagsHelper()->searchDataInReport($sortedReport[$i]),
                "Report sorting by $column is not correct");
        };
    }

    /**
     * Performs report export
     *
     * @return array|bool
     */
    public function export()
    {
        $exportUrl = $this->getSelectedValue($this->_getControlXpath('dropdown', 'export_to'));
        $report = $this->_getFile($exportUrl);
        if (strpos(strtolower($exportUrl), 'csv')) {
            return $this->csvToArray($report);
        }
        if (strpos(strtolower($exportUrl), 'excel')) {
            $xmlArray = $this->xmlToArray($report);
            return $this->convertXmlReport($xmlArray);
        }
        return false;
    }

    /**
     * Get file from admin area
     * Suitable for export testing
     *
     * @param string $url Url to the file or submit form
     * @return string
     */
    protected function _getFile($url)
    {
        $cookie = $this->getCookie();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * Converts CSV string to associative array
     *
     * @param string $input Input csv string to be converted to array
     * @param string $delimiter Delimiter
     * @return array
     */
    public function csvToArray($input, $delimiter = ',')
    {
        $temp = tmpfile();
        fwrite($temp, $input);
        fseek($temp, 0);
        $data   = array();
        $header = null;
        while (($line = fgetcsv($temp, 10000, $delimiter, '"', '\\')) !== FALSE) {
            if (!$header) {
                $header = $line;
            } else {
                try {
                    $data[] = array_combine($header, $line);
                } catch (Exception $e) {
                    //invalid format
                    return null;
                }
            }
        }
        return $data;
    }

    /**
     * Converts XML string to associative array
     *
     * @param string $xmlString Input xml string to be converted to array
     * @return array
     */
    public function xmlToArray($xmlString)
    {
        $xmlArray = json_decode(json_encode((array) simplexml_load_string($xmlString)), 1);
        return $xmlArray;
    }

    /**
     * Converts report xml array into readable associative array
     *
     * @param string $xmlArray Input array to be converted
     * @return array
     */
    public function convertXmlReport($xmlArray)
    {
        $newXmlArray = $xmlArray['Worksheet']['Table']['Row'];
        $keys = array();
        $values = array();
        foreach ($newXmlArray[0]['Cell'] as $key => $value) {
            $keys[] = $newXmlArray[0]['Cell'][$key]['Data'];
        }
        for ($i = 1; $i < count($newXmlArray); $i++) {
            foreach ($newXmlArray[$i]['Cell'] as $key => $value) {
                $values[$i-1][] =  $newXmlArray[$i]['Cell'][$key]['Data'];
            }
        }
        $convertedXmlArray = array();
        foreach ($values as $key => $value) {
            $convertedXmlArray[] = array_combine($keys, $values[$key]);
        }
        return $convertedXmlArray;
    }

    /**
     * Verifies if exported tag report contains correct data
     *
     * @param array $expectedData Data expected to be in exported file
     * @param array $exportedData Exported tag report from csv or xml file
     * @return void
     */
    public function verifyExportedReport(array $expectedData, array $exportedData)
    {
        $matchExpectedAndExportData = array();
        foreach ($expectedData as $key => $dataRow) {
            $matchExpectedAndExportData[] = array_intersect_assoc($exportedData[$key],
                $expectedData[$key]);
        }
        $this->assertEquals($matchExpectedAndExportData, $expectedData,
            'Report was not exported correctly');
    }
}