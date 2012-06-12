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
class Core_Mage_Category_Helper extends Mage_Selenium_TestCase
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
        $categoryText = '/div/a/span';

        if (!$parentCategoryId) {
            $this->addParameter('rootName', $catName);
            $catXpath =
                $this->_getControlXpath('link', 'root_category', $this->_findUimapElement('fieldset', $fieldsetName));
        } else {
            $this->addParameter('parentCategoryId', $parentCategoryId);
            $this->addParameter('subName', $catName);
            $isDiscloseCategory =
                $this->_getControlXpath('link', 'expand_category', $this->_findUimapElement('fieldset', $fieldsetName));
            $catXpath =
                $this->_getControlXpath('link', 'sub_category', $this->_findUimapElement('fieldset', $fieldsetName));
            if ($this->isElementPresent($isDiscloseCategory)) {
                $this->click($isDiscloseCategory);
                $this->pleaseWait();
            }
        }
        $this->waitForAjax();
        $qtyCat = $this->getXpathCount($catXpath . $categoryText);

        for ($i = 1; $i <= $qtyCat; $i++) {
            $text = $this->getText($catXpath . '[' . $i . ']' . $categoryText);
            $text = preg_replace('/ \([0-9]+\)/', '', $text);
            if ($catName === $text) {
                $isCorrectName[] = $this->getAttribute($catXpath . '[' . $i . ']' . '/div/a/@id');
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
        $nodes = explode('/', $categoryPath);
        $rootCat = array_shift($nodes);
        $categoryContainer = "//*[@id='category-edit-container']//h3";

        $correctRoot = $this->defineCorrectCategory($rootCat, null, $fieldsetName);

        foreach ($nodes as $value) {
            $correctSubCat = array();
            foreach ($correctRoot as $v) {
                $correctSubCat = array_merge($correctSubCat, $this->defineCorrectCategory($value, $v, $fieldsetName));
            }
            $correctRoot = $correctSubCat;
        }

        if ($correctRoot) {
            $this->click('//*[@id=\'' . array_shift($correctRoot) . '\']');
            if ($this->isElementPresent($categoryContainer)) {
                $this->pleaseWait();
            }
            if ($nodes) {
                $pageName = end($nodes);
            } else {
                $pageName = $rootCat;
            }
            if ($this->isElementPresent($categoryContainer)) {
                $openedPageName = $this->getText($categoryContainer);
                $openedPageName = preg_replace('/ \(ID\: [0-9]+\)/', '', $openedPageName);
                if ($pageName != $openedPageName) {
                    $this->fail("Opened category with name '$openedPageName' but must be '$pageName'");
                }
            }
        } else {
            $this->fail("Category with path='$categoryPath' could not be selected");
        }
    }

    /**
     * Fill in Category information
     *
     * @param array $categoryData
     */
    public function fillCategoryInfo(array $categoryData)
    {
        $page = $this->getCurrentUimapPage();
        $tabs = $page->getAllTabs();
        foreach ($tabs as $tab => $values) {
            if ($tab != 'category_products') {
                $this->fillForm($categoryData, $tab);
            } else {
                $arrayKey = $tab . '_data';
                $this->openTab($tab);
                if (array_key_exists($arrayKey, $categoryData) && is_array($categoryData[$arrayKey])) {
                    foreach ($categoryData[$arrayKey] as $value) {
                        $this->productHelper()->assignProduct($value, $tab);
                    }
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
        if (is_string($categoryData)) {
            $elements = explode('/', $categoryData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $categoryData = $this->loadDataSet($fileName, implode('/', $elements));
        }
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
        $waitCondition = array("//*[@id='category-edit-container']//h3[not(contains(text(),'New'))]",
                               $this->_getMessageXpath('general_error'), $this->_getMessageXpath('general_validation'));
        $this->clickButton('save_category', false);
        $this->waitForElement($waitCondition);
        $this->validatePage();
    }

    /**
     * check that Categories Page is opened
     */
    public function checkCategoriesPage()
    {
        $this->addParameter('id', $this->defineIdFromUrl());
        $currentPage = $this->_findCurrentPageFromUrl();
        if (!in_array($currentPage, array('edit_manage_categories', 'manage_categories', 'edit_category'))) {
            $this->fail("Opened the wrong page: '" . $currentPage . "' (should be: 'manage_categories')");
        }
        $this->setCurrentPage($currentPage);
    }

    /**
     * Click button and confirm
     *
     * @param string $buttonName
     * @param string $message
     *
     * @return bool
     */
    public function deleteCategory($buttonName, $message)
    {
        $buttonXpath = $this->_getControlXpath('button', $buttonName);
        if ($this->isElementPresent($buttonXpath)) {
            $confirmation = $this->getCurrentUimapPage()->findMessage($message);
            $this->chooseCancelOnNextConfirmation();
            $this->click($buttonXpath);
            if ($this->isConfirmationPresent()) {
                $text = $this->getConfirmation();
                if ($text == $confirmation) {
                    $this->chooseOkOnNextConfirmation();
                    $this->click($buttonXpath);
                    $this->getConfirmation();
                    $this->pleaseWait();

                    return true;
                } else {
                    $this->addVerificationMessage("The confirmation text incorrect: {$text}");
                }
            } else {
                $this->addVerificationMessage("The confirmation does not appear");
                $this->pleaseWait();
                $this->validatePage();

                return true;
            }
        } else {
            $this->addVerificationMessage("There is no way to remove an item(There is no 'Delete' button)");
        }

        return false;
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
        if (is_string($productsInfo)) {
            $elements = explode('/', $productsInfo);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $productsInfo = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $category = (isset($productsInfo['category'])) ? $productsInfo['category'] : NULL;
        $productName = (isset($productsInfo['product_name'])) ? $productsInfo['product_name'] : NULL;
        $verificationData = (isset($productsInfo['verification'])) ? $productsInfo['verification'] : array();

        if (!is_null($category) && !is_null($productName)) {
            $foundIt = $this->frontSearchAndOpenPageWithProduct($productName, $category);
            if (!$foundIt) {
                $this->fail('Could not find the product');
            }
            $this->frontVerifyProductPrices($verificationData, $productName);
        } else {
            $this->fail('Category or product name is not specified');
        }
    }

    /**
     * OpenCategory
     *
     * @param string $categoryPath
     *
     * @return boolean
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
        $this->addParameter('categoryTitle', $title);
        //Form category xpath
        $link = '//ul[@id="nav"]';
        foreach ($nodes as $node) {
            $link = $link . '//li[contains(a/span,"' . $node . '")]';
        }
        $link = $link . '/a';
        if ($this->isElementPresent($link)) {
            //Determine category mca parameters
            $mca = self::_getMcaFromCurrentUrl($this->_configHelper->getConfigAreas(),
                $this->getAttribute($link . '@href'));
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
            $this->clickAndWait($link, $this->_browserTimeoutPeriod);
            $this->validatePage();
            return true;
        }
        $this->fail('"' . $categoryPath . '" category page could not be opened');
        return false;
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
        $xpathNext = $this->_getControlXpath('link', 'next_page');
        $xpathProduct = $this->_getControlXpath('pageelement', 'product_name_header');

        $i = 1;
        for (; ;) {
            if ($this->isElementPresent($xpathProduct)) {
                return $i;
            } elseif ($this->isElementPresent($xpathNext)) {
                $i++;
                $this->addParameter('categoryParam', '?p=' . $i);
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
     * @param string $productName
     */
    public function frontVerifyProductPrices(array $verificationData, $productName = '')
    {
        if ($productName) {
            $productName = "Product with name '$productName': ";
        }
        $pageelements = $this->getCurrentUimapPage()->getAllPageelements();
        foreach ($verificationData as $key => $value) {
            $this->addParameter('price', $value);
            $xpathPrice = $this->getCurrentUimapPage()->findPageelement($key);
            if (!$this->isElementPresent($xpathPrice)) {
                $this->addVerificationMessage(
                    $productName . 'Could not find element ' . $key . ' with price ' . $value);
            }
            unset($pageelements['ex_' . $key]);
        }
        foreach ($pageelements as $key => $value) {
            if (preg_match('/^ex_/', $key) && $this->isElementPresent($value)) {
                $this->addVerificationMessage($productName . 'Element ' . $key . ' is on the page');
            }
        }

        $this->assertEmptyVerificationErrors();
    }

    /**
     * Moves categories
     *
     * @param string $whatCatName
     * @param string $whereCatName
     */
    public function moveCategory($whatCatName, $whereCatName)
    {
        $xpathWhatCatName = "//span[contains(.,'" . $whatCatName . "')]";
        $xpathWhereCatName = "//span[contains(.,'" . $whereCatName . "')]";
        if ($this->isElementPresent($xpathWhatCatName) && $this->isElementPresent($xpathWhereCatName)) {
            $this->clickAt($xpathWhatCatName, '5,2');
            $this->waitForAjax();
            $this->mouseDownAt($xpathWhatCatName, '5,2');
            $this->mouseMoveAt($xpathWhereCatName, '20,10');
            $this->waitForAjax();
            $this->mouseUpAt($xpathWhereCatName, '20,10');
        } else {
            $this->fail('Cannot find elements to move');
        }
    }
}