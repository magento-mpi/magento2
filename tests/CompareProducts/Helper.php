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
        //@TODO Temporary workaround
        $this->appendParamsDecorator($this->categoryHelper()->_paramsHelper);
        $this->clickControl('link', 'add_to_compare');
    }

     /**
     * Add product from Product page
     *
     *
     * @param array $productName  Name of product to be added
     * @param array $categoryName  Product Category
    */
    public function frontAddProductToCompareFromProductPage($productName,$categoryName=null)
    {
        $this->productHelper()->frontOpenProduct($productName,$categoryName);
        //@TODO Temporary workaround
        $this->appendParamsDecorator($this->productHelper()->_paramsHelper);
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
        return $this->clickControlAndConfirm ('link', 'clear_all', 'confirmation_clear_all_from_compare');
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
                'link', 'delete_product', 'confirmation_for_removing_product_from_compare');
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
        if (key_exists($productName, $compareProducts) and count($compareProducts)>=3) {
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
        $page = $this->getCurrentLocationUimapPage();
        $pageelements = $page->getAllElements('pageelements',$this->_paramsHelper);
        foreach ($pageelements as $key => $value) {
            if ($this->isElementPresent($value) and !(strpos($key,'verify')===FALSE))
                $productData[$key]= trim ($this->getText($value), " $\n\t\r\0");
        }
        //GetAttributes
        $attributesList = $this->frontGetAttributesListComparePopup();
        foreach ($attributesList as $key => $value) {
            $this->addParameter('attrName', $key);
            $attrValueXpath = $this->_getControlXpath('pageelement', 'product_attribute_value');
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
        $attrXPath = $this->getCurrentLocationUimapPage()->findPageelement('product_attribute_names');
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
        $productsXPath = $this->getCurrentLocationUimapPage()->findPageelement('product_names');
        $productsList = $this->getElementsText($productsXPath, "//h2[@class='product-name']");
        return $productsList;
    }

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
            $compareProductsData[$key] = $this->frontGetProductDetailsFromComparePopup($key,$value );
        }
        if (count($productsData)!=count($compareProductsData))
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
                         if (strcmp((string)$value, $productToVerify[$key])!=0) {
                            $this->messages['error'][] =
                                    "Values are not identical: $value and $productToVerify[$key]";
                        }
                    }else {
                        $this->messages['error'][] =
                                "There is no such property $key for "  . $compareProductName;
                    }
                }
           }  else {
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
       $this->clickButton('compare',false);
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
                $this->clickButton ('close_window',false);
                //select parent window
                $this->selectWindow(null);
                $this->popupId = null;
        }
    }

    public function prepareProductForVerify($productsData)
    {
        $dataForVerify = array();
         foreach ($productsData as $key => $productData) {
             if (isset ($productData['prices_special_price'])) {
                 $dataForVerify[$productData['general_name']]['verify_price_special'] = $productData['prices_special_price'];
                 $dataForVerify[$productData['general_name']]['verify_price_special_excluding_tax'] = $productData['prices_special_price'];
                 $dataForVerify[$productData['general_name']]['verify_price_special_inlcuding_tax'] = $productData['prices_special_price'];
                 $dataForVerify[$productData['general_name']]['verify_ex_price_special_excluding_tax'] = $productData['prices_special_price'];
                 $dataForVerify[$productData['general_name']]['verify_ex_price_special_inlcuding_tax'] = $productData['prices_special_price'];

             }else {
                 $dataForVerify[$productData['general_name']]['verify_price_regular'] = $productData['prices_price'];
                 $dataForVerify[$productData['general_name']]['verify_ex_price_regular'] = $productData['prices_price'];
                 $dataForVerify[$productData['general_name']]['verify_ex_price_excluding_tax'] = $productData['prices_price'];
                 $dataForVerify[$productData['general_name']]['verify_ex_price_including_tax'] = $productData['prices_price'];
                 $dataForVerify[$productData['general_name']]['verify_price_excluding_tax'] = $productData['prices_price'];
                 $dataForVerify[$productData['general_name']]['verify_price_including_tax'] = $productData['prices_price'];
             }
             $dataForVerify[$productData['general_name']]['Description'] = $productData['general_description'];
             $dataForVerify[$productData['general_name']]['Short Description'] = $productData['general_short_description'];
             $dataForVerify[$productData['general_name']]['SKU'] = $productData['general_sku'];
             //Add additional non-standart attributes
             #$dataForVerify[$key]['Weight'] = $productData['general_weight'];
        }
        return $dataForVerify;
    }
}
