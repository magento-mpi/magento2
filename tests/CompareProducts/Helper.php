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
class CompareProducts_Helper extends Mage_Selenium_TestCase
{

    protected $popupId = null;

    /**
     * Add product from Catalog page
     *
     *
     * @param array $productName  Name of product to be added
     * @param array $categoryName  Products Category
     */
    public function frontAddProductToCompareFromCatalogPage($productName, $categoryName)
    {
        $pageId = $this->categoryHelper()->frontSearchAndOpenPageWithProduct($productName, $categoryName);
        if (!$pageId)
            $this->fail('Could not find the product');
        $this->appendParamsDecorator($this->categoryHelper()->_paramsHelper); //@TODO Temporary workaround
        $this->clickControl('link', 'add_to_compare');
    }

    /**
     * Add product from Product page
     *
     *
     * @param array $productName  Name of product to be added
     * @param array $categoryName  Product Category
     */
    public function frontAddProductToCompareFromProductPage($productName, $categoryName=null)
    {
        $this->productHelper()->frontOpenProduct($productName, $categoryName);
        $this->appendParamsDecorator($this->productHelper()->_paramsHelper); //@TODO Temporary workaround
        $this->clickControl('link', 'add_to_compare');
    }

    /**
     * Removes all products from the Compare Products widget
     *
     * Preconditions: page with Compare Products widget is opened
     *
     */
    public function frontClearAll()
    {
        if ($this->controlIsPresent('pageelement', 'compare_block_title')) {
            return $this->clickControlAndConfirm('link', 'compare_clear_all', 'confirmation_clear_all_from_compare');
        }
        else
            $this->fail('Compare static block is not available on this page');
    }

    /**
     * Removes product from the Compare Products block
     *
     * Preconditions: page with Compare Products block is opened
     *
     * @param string $productName Name of product to be deleted
     */
    public function frontRemoveProductFromCompareBlock($productName)
    {
        $this->addParameter('productName', $productName);
        return $this->clickControlAndConfirm(
                        'link', 'compare_delete_product', 'confirmation_for_removing_product_from_compare');
    }

    /**
     * Removes product from the Compare Products pop-up
     *
     * Preconditions: Compare Products pop-up is opened
     *
     * @param string $productName Name of product to be deleted
     */
    public function frontRemoveProductFromComparePopup($productName)
    {
        $compareProducts = $this->frontGetProductsListComparePopup();
        if (key_exists($productName, $compareProducts) and count($compareProducts) >= 3) {
            $this->addParameter('columnIndex', $compareProducts[$productName]);
            $this->clickControl('link', 'remove_item');
            return true;
        }
        return false;
    }

    /**
     * Get available product details from the Compare Products pop-up
     *
     * Preconditions: Compare Products pop-up is opened
     *
     * @param string $productName Name of product to be grabbed
     * @param string $productIndex Index of product to be grabbed
     *
     * @return array $productData Product details from Compare Products pop-up
     */
    public function frontGetProductDetailsFromComparePopup($productName, $productIndex)
    {
        $productData = array();
        $this->addParameter('productName', $productName);
        $this->addParameter('columnIndex', $productIndex);
        $pageelements = $this->getCurrentUimapPage()->getAllElements('pageelements', $this->_paramsHelper);
        foreach ($pageelements as $key => $value) {
            if ($this->isElementPresent($value) and strpos($key, 'verify') !== FALSE)
                $productData[$key] = trim($this->getText($value), " $\n\t\r\0");
        }
        //GetAttributes
        $attributesList = $this->frontGetAttributesListComparePopup();
        foreach ($attributesList as $key => $value) {
            $this->addParameter('attrName', $key);
            $attrValueXpath = $this->_getControlXpath('pageelement', 'product_attribute_value');
            $key = trim(strtolower(preg_replace('#[^0-9a-z]+#i', '_', $key)), '_');
            $productData[$key] = $this->getText($attrValueXpath);
        }
        return $productData;
    }

    /**
     * Get list of available product attributes in Compare Products pop-up
     *
     * Preconditions: Compare Products pop-up is opened
     *
     * @return array $attributesList Array of available product attributes in Compare Products pop-up
     *
     */
    public function frontGetAttributesListComparePopup()
    {
        $attrXPath = $this->_getControlXpath('pageelement', 'product_attribute_names');
        $attributesList = $this->getElementsText($attrXPath, "/th/span");
        return $attributesList;
    }

    /**
     * Get list of available products in Compare Products pop-up
     *
     * Preconditions: Compare Products pop-up is opened
     *
     */
    public function frontGetProductsListComparePopup()
    {
        $productsXPath = $this->_getControlXpath('pageelement', 'product_names');
        $productsList = $this->getElementsText($productsXPath, "//*[@class='product-name']");
        return $productsList;
    }

    /**
     * Gets text for all element(s) by XPath
     *
     * @param string $elementsXpath General XPath of looking up element(s)
     * @param string $additionalXPath Additional XPath (by defauilt = '')
     *
     * @return array Array of elements text with id of element
     */
    public function getElementsText($elementsXpath, $additionalXPath = '')
    {
        $elements = array();
        $totalElements = $this->getXpathCount($elementsXpath);
        for ($i = 1; $i < $totalElements + 1; $i++) {
            $elementXpath = $elementsXpath . "[$i]" . $additionalXPath;
            $elementValue = $this->getText($elementXpath);
            $elements[$elementValue] = $i;
        }
        return $elements;
    }

    /**
     * Compare provided products data with actual info in Compare Products pop-up
     *
     * Preconditions: Compare Products pop-up is opened
     *
     * @param array $productsData Array of products info to be checked
     * @return array Array of  error messages if any
     */
    public function frontVerifyProductDataInComparePopup($productsData)
    {
        //reset error messages
        $this->messages['error'] = array();
        //get list of products
        $compareProducts = $this->frontGetProductsListComparePopup();
        //get details for each product
        foreach ($compareProducts as $key => $value) {
            $compareProductsData[$key] = $this->frontGetProductDetailsFromComparePopup($key, $value);
        }
        if (count($productsData) != count($compareProductsData))
            $this->messages['error'][] = "Unxepected number of products on Compare popup:"
                    . "expected " . count($productsData) . "; actual " . count($compareProductsData);
        //compare aarays
        foreach ($compareProductsData as $compareProductName => $compareProductData) {
            //product exists on compare popup
            if (key_exists($compareProductName, $productsData)) {
                $productToVerify = $productsData[$compareProductName];
                //check product properties
                foreach ($compareProductData as $key => $value) {
                    if (key_exists($key, $productToVerify)) {
                        if (strcmp($value, $productToVerify[$key]) != 0) {
                            $this->messages['error'][] =
                                    "Values are not identical: $value and $productToVerify[$key]";
                        }
                        unset($compareProductData[$key]);
                    } else {
                        $this->messages['error'][] =
                                "There is no such property $key for " . $compareProductName;
                    }
                }
            } else {
                $this->messages['error'][] =
                        'There is unexpected product ' . $compareProductName . ' on Compare page';
            }
        }
        return $this->messages;
    }

    /**
     * Open ComparePopup And set focus
     *
     * Preconditions: Page with Compare block is opened
     */
    public function frontOpenComparePopup()
    {
        $this->clickButton('compare', false);
        $names = $this->getAllWindowNames();
        $this->popupId = end($names);
        $this->waitForPopUp($this->popupId, $this->_browserTimeoutPeriod);
        $this->selectWindow("name=" . $this->popupId);
        $this->validatePage('compare_products');
    }

    /**
     * Close ComparePopup and set focus to main window
     *
     * Preconditions: ComparePopup is opened
     */
    public function frontCloseComparePopup()
    {
        if ($this->popupId) {
            $this->selectWindow("name=" . $this->popupId);
            $this->clickButton('close_window', false);
            //select parent window
            $this->selectWindow(null);
            $this->popupId = null;
        }
    }

    /**
     * Will load additional product information to verify
     *
     * @return array Product information
     */
    public function prepareProductForVerify($productsData)
    {
        $dataForVerify = array();
        foreach ($productsData as $productData) {
            $key = $productData['general_name'];
            foreach ($productData as $attribute => $value) {
                //remove 'general_'
                $newAttributeName = preg_replace('/^[0-9a-z]+_/', '', $attribute);
                $dataForVerify[$key][$newAttributeName] = $value;
            }
            $additionalProductData = $this->loadData('additional_product_data');
            $dataForVerify[$key] = array_merge($dataForVerify[$key], $additionalProductData);
        }
        return $dataForVerify;
    }

}
