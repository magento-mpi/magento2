<?php

/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Category
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
class Core_Mage_Category_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Find category with valid name
     *
     * @param string $catName
     * @param null|string $parentCategoryId
     * @param string $fieldsetName
     *
     * @return array
     */
    public function defineCorrectCategory($catName, $parentCategoryId = null, $fieldsetName = 'select_category')
    {
        $isCorrectName = array();

        if (!$parentCategoryId) {
            $this->addParameter('rootName', $catName);
            $categoryType = 'root_category';
        } else {
            $this->addParameter('parentCategoryId', $parentCategoryId);
            $this->addParameter('subName', $catName);
            $categoryType = 'sub_category';
            if ($this->controlIsPresent('link', $fieldsetName . '_expand_category')) {
                $this->clickControl('link', $fieldsetName . '_expand_category', false);
                $this->pleaseWait();
            }
        }
        $fieldsetXpath = $this->_getControlXpath('fieldset', $fieldsetName);
        $categoryXpath = $this->_getControlXpath('link', $fieldsetName . '_' . $categoryType);
        $this->addParameter('categoryXpath', str_replace($fieldsetXpath, '', $categoryXpath));
        if ($this->controlIsPresent('pageelement', $fieldsetName . '_category_text')) {
            $elements = $this->getControlElements('pageelement', $fieldsetName . '_category_text');
            /* @var PHPUnit_Extensions_Selenium2TestCase_Element $element */
            foreach ($elements as $key => $element) {
                $actualCatName = preg_replace('/ \([0-9]+\)/', '', $element->text());
                if ($catName === $actualCatName) {
                    $this->addParameter('index', $key + 1);
                    $isCorrectName[] =
                        $this->getControlAttribute('pageelement', $fieldsetName . '_category_index_link', 'id');
                }
            }
        }

        return $isCorrectName;
    }

    /**
     * Select category by path
     *
     * @param string $categoryPath
     * @param string $fieldsetName
     */
    public function selectCategory($categoryPath, $fieldsetName = 'select_category')
    {
        $this->waitForControlEditable('fieldset', $fieldsetName);
        foreach (explode('/', $categoryPath) as $categoryNode) {
            if (isset($trueCategory)) {
                $trueSubCat = array();
                foreach ($trueCategory as $data) {
                    $trueSubCat =
                        array_merge($trueSubCat, $this->defineCorrectCategory($categoryNode, $data, $fieldsetName));
                }
                $trueCategory = $trueSubCat;
            } else {
                $trueCategory = $this->defineCorrectCategory($categoryNode, null, $fieldsetName);
            }
            if (empty($trueCategory)) {
                list($path) = explode($categoryNode, $categoryPath);
                $this->fail("'$categoryNode' category with path = '$path' could not found.");
            }
        }
        list($categoryId) = $trueCategory;
        $isCategoriesPage = $this->isCategoriesPage();
        if (strpos($categoryId, "'") !== false) {
            $categoryId = "concat('" . str_replace('\'', "',\"'\",'", $categoryId) . "')";
        }
        $this->addParameter('elementId', $categoryId);
        $element = $this->getChildElement($this->getControlElement('pageelement', 'element_by_id'), 'span');
        $this->focusOnElement($element);
        $element->click();
        if ($isCategoriesPage) {
            $this->pleaseWait();
            $openedPageName = $this->getControlAttribute('pageelement', 'category_name_header', 'text');
            $openedPageName = preg_replace('/ \(ID\: [0-9]+\)/', '', $openedPageName);
            $nodes = explode('/', $categoryPath);
            $expectedName = end($nodes);
            if ($expectedName != $openedPageName) {
                $this->fail("Opened category with name '$openedPageName' but must be '$expectedName'");
            }
        }
    }

    /**
     * Fill in Category information
     *
     * @param array $categoryData
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function fillCategoryInfo(array $categoryData)
    {
        $tabs = $this->getCurrentUimapPage()->getAllTabs();
        foreach ($tabs as $tab => $values) {
            if (!$this->controlIsPresent('tab', $tab)) {
                continue;
            }
            if ($tab != 'category_products') {
                $this->fillTab($categoryData, $tab, false);
                continue;
            }
            $arrayKey = $tab . '_data';
            if (array_key_exists($arrayKey, $categoryData) && is_array($categoryData[$arrayKey])) {
                $this->openTab($tab);
                foreach ($categoryData[$arrayKey] as $value) {
                    $this->productHelper()->assignProduct($value, $tab);
                }
            }
        }
    }

    /**
     *
     * @param array $categoryData
     */
    public function createCategory($categoryData)
    {
        $this->pleaseWait();
        $categoryData = $this->fixtureDataToArray($categoryData);
        if (array_key_exists('parent_category', $categoryData)) {
            $this->selectCategory($categoryData['parent_category']);
            $this->clickButton('add_sub_category', false);
        } else {
            $this->clickButton('add_root_category', false);
        }
        $this->pleaseWait();
        $this->fillCategoryInfo($categoryData);
        if (isset($categoryData['name'])) {
            $this->addParameter('elementTitle', $categoryData['name']);
        }
        $waitCondition = array(
            $this->_getMessageXpath('general_error'),
            $this->_getControlXpath('pageelement', 'created_category_name_header'),
            $this->_getMessageXpath('general_validation')
        );
        $this->clickButton('save_category', false);
        $this->waitForElementVisible($waitCondition);
        $this->pleaseWait();
        $this->checkCategoriesPage();
    }

    /**
     * check that Categories Page is opened
     */
    public function checkCategoriesPage()
    {
        if (!$this->isCategoriesPage()) {
            $this->fail("Opened page is not 'manage_categories' page");
        }
    }

    /**
     * @return bool
     */
    public function isCategoriesPage()
    {
        $this->addParameter('id', $this->defineIdFromUrl());
        $currentPage = $this->_findCurrentPageFromUrl();
        if (!in_array($currentPage, array('edit_manage_categories', 'manage_categories', 'edit_category'))) {
            return false;
        }
        $this->setCurrentPage($currentPage);
        return true;
    }

    /**
     * Click button and confirm
     *
     * @param string $buttonName
     * @param string $message
     */
    public function deleteCategory($buttonName, $message)
    {
        $locator = $this->_getControlXpath('button', $buttonName);
        $availableElement = $this->elementIsPresent($locator);
        if (!$availableElement) {
            $this->fail("There is no way to remove a category(There is no 'Delete' button)");
        }
        $confirmation = $this->_getMessageXpath($message);
        $availableElement->click();
        $actualText = $this->alertText();
        if ($actualText != $confirmation) {
            $this->fail("The confirmation text incorrect: '$actualText' != '$confirmation''");
        }
        $this->acceptAlert();
        $this->pleaseWait();
        $this->checkCategoriesPage();
    }

    /**
     * Validates product information in category
     *
     * @param array|string $productsInfo
     *
     * @return bool
     */
    public function frontOpenCategoryAndValidateProduct($productsInfo)
    {
        $productsInfo = $this->fixtureDataToArray($productsInfo);
        $category = (isset($productsInfo['category'])) ? $productsInfo['category'] : null;
        $productName = (isset($productsInfo['product_name'])) ? $productsInfo['product_name'] : null;
        $verificationData = (isset($productsInfo['verification'])) ? $productsInfo['verification'] : array();
        if (is_null($category) || is_null($productName)) {
            $this->fail('Category or product name is not specified');

        }
        $foundIt = $this->frontSearchAndOpenPageWithProduct($productName, $category);
        if (!$foundIt) {
            $this->fail('Could not find the product');
        }
        $this->frontVerifyProductPrices($verificationData);
    }

    /**
     * Determine category mca parameters
     * @param $mca
     */
    protected function _determineMcaParams(&$mca)
    {
        if (preg_match('/\.html$/', $mca)) {
            if (preg_match('|/|', $mca)) {
                $mcaNodes = explode('/', $mca);
                if (count($mcaNodes) > 2) {
                    $this->fail('@TODO not work with nested categories, more then 2');
                }
                $this->addParameter('rotCategoryUrl', $mcaNodes[0]);
                $this->addParameter('categoryUrl', preg_replace('/\.html$/', '', $mcaNodes[1]));
            } else {
                $this->addParameter('categoryUrl', preg_replace('/\.html$/', '', $mca));
            }
        } else {
            $mcaNodes = explode('/', $mca);
            foreach ($mcaNodes as $key => $value) {
                if ($value == 'id' && isset($mcaNodes[$key + 1])) {
                    $this->addParameter('id', $mcaNodes[$key + 1]);
                }
                if ($value == 's' && isset($mcaNodes[$key + 1])) {
                    $this->addParameter('categoryUrl', $mcaNodes[$key + 1]);
                }
            }
        }
    }

    /**
     * OpenCategory
     *
     * @param string $categoryPath
     */
    public function frontOpenCategory($categoryPath)
    {
        //Determine category title
        $nodes = explode('/', $categoryPath);
        $nodesReverse = array_reverse($nodes);
        $title = '';
        foreach ($nodesReverse as $key => $value) {
            $title .= $value;
            if (isset($nodes[$key + 1])) {
                $title .= ' - ';
            }
        }
        $this->addParameter('elementTitle', $title);
        //Form category xpath
        $link = "//ul[@id='nav']";
        foreach ($nodes as $node) {
            $link = $link . '//li[contains(a/span,"' . $node . '")]';
        }
        $link = $link . '/a';
        $availableElement = $this->elementIsPresent($link);
        if (!$availableElement) {
            $this->fail('"' . $categoryPath . '" category page could not be opened');
        }
        //Determine category mca parameters
        $mca = $this->getMcaFromUrl($availableElement->attribute('href'));
        $this->_determineMcaParams($mca);
        $availableElement->click();
        $this->waitForPageToLoad();
        $this->validatePage();

    }

    /**
     * Searches the page with the product in the category
     *
     * @param string $productName
     * @param string $category
     *
     * @return mixed
     */
    public function frontSearchAndOpenPageWithProduct($productName, $category)
    {
        $this->frontOpenCategory($category);
        $this->addParameter('productName', $productName);
        $value = 1;
        for (; ;) {
            if ($this->controlIsPresent('pageelement', 'product_name')) {
                return $value;
            } elseif ($this->controlIsPresent('link', 'next_page')) {
                $value++;
                $this->addParameter('categoryParam', '?p=' . $value);
                $this->navigate('category_page_index');
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Verifies the correctness of prices in the category
     *
     * @param array $verificationData
     * @param string $currency
     */
    public function frontVerifyProductPrices(array $verificationData, $currency = '$')
    {
        $productName = $this->getControlAttribute('pageelement', 'product_name', 'text');
        if (isset($verificationData['qty'])) {
            $actualQty = $this->getControlAttribute('field', 'product_qty', 'selectedValue');
            $this->assertSame($verificationData['qty'], $actualQty, 'Wrong Qty for ' . $productName . ' product');
            unset($verificationData['qty']);
        }
        $prices = $this->productHelper()->getFrontendProductPrices();
        foreach ($verificationData as $priceName => $priceValue) {
            $verificationData[$priceName] = floatval(str_replace($currency, '', $priceValue));
        }
        $this->assertEquals($verificationData, $prices, 'Wrong prices for ' . $productName . ' product');
    }

    /**
     * Moves categories
     *
     * @param string $whatCatName
     * @param string $whereCatName
     */
    public function moveCategory($whatCatName, $whereCatName)
    {
        $this->addParameter('categoryName', $whatCatName);
        $this->moveto($this->getControlElement('link', 'category_by_name'));
        $this->buttondown();
        $this->addParameter('categoryName', $whereCatName);
        $this->moveto($this->getControlElement('link', 'category_by_name'));
        $this->buttonup();
        $this->pleaseWait();
    }
}
