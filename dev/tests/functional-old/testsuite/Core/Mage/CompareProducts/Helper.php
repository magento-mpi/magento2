<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CompareProducts
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
class Core_Mage_CompareProducts_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Add product from Catalog page
     *
     * @param array $productName Name of product to be added
     * @param array $categoryName Products Category
     */
    public function frontAddToCompareFromCatalogPage($productName, $categoryName)
    {
        if (!$this->categoryHelper()->frontSearchAndOpenPageWithProduct($productName, $categoryName)) {
            $this->fail('Could not find "' . $productName . '" product on "' . $categoryName . '" category page');
        }
        $this->moveto($this->getControlElement('pageelement', 'product_name'));
        $this->clickControl('link', 'add_to_compare');
    }

    /**
     * Add product from Product page
     *
     * @param array $productName Name of product to be added
     */
    public function frontAddToCompareFromProductPage($productName)
    {
        $this->productHelper()->frontOpenProduct($productName);
        $this->clickControl('link', 'add_to_compare');
    }

    /**
     * Removes all products from the Compare Products widget
     *
     * Preconditions: page with Compare Products widget should be opened
     *
     * @return bool Returns False if the operation could not be performed
     * or the compare block is not present on the page
     */
    public function frontClearAll()
    {
        if (!$this->controlIsVisible('fieldset', 'compare_products_block')) {
            return false;
        }
        if ($this->controlIsVisible('link', 'compare_clear_all')) {
            $this->clickControlAndConfirm('link', 'compare_clear_all', 'confirmation_clear_all_from_compare');
        }
        return true;
    }

    /**
     * Removes product from the Compare Products block
     * Preconditions: page with Compare Products block is opened
     *
     * @param string $productName Name of product to be deleted
     */
    public function frontRemoveProductFromCompareBlock($productName)
    {
        $this->addParameter('productName', $productName);
        $this->clickControlAndConfirm('link', 'compare_delete_product',
            'confirmation_for_removing_product_from_compare');
    }

    /**
     * Removes product from the Compare Products pop-up
     * Preconditions: Compare Products pop-up is opened
     *
     * @param string $productName Name of product to be deleted
     *
     * @return bool
     */
    public function frontRemoveProductFromComparePopup($productName)
    {
        $compareProducts = $this->frontGetProductsListComparePopup();
        //if (array_key_exists($productName, $compareProducts) and count($compareProducts) >= 3) {
        $this->addParameter('columnIndex', $compareProducts[$productName]);
        $this->clickControl('link', 'remove_item');
        return true;
        //}
        //return false;
    }

    /**
     * Get Field Names
     * @param $names
     * @return array $arrayNames
     */
    protected function _getFieldNames($names)
    {
        $arrayNames = array('remove', 'product_name');
        $names = array_diff($names, array(''));
        foreach ($names as $value) {
            $arrayNames[] = $value;
        }
        return $arrayNames;
    }

    /**
     * Get available product details from the Compare Products pop-up
     *
     * Preconditions: Compare Products pop-up is opened
     *
     * @return array $productData Product details from Compare Products pop-up
     */
    public function getProductDetailsOnComparePage()
    {
        $data = array();
        $names = array();
        /** @var PHPUnit_Extensions_Selenium2TestCase_Element $cellData */
        foreach ($this->getControlElements('pageelement', 'compare_column_names') as $cellData) {
            $names[] = trim($cellData->text());
        }
        $names = $this->_getFieldNames($names);

        $productCount = $this->getControlCount('pageelement', 'product_names');
        $table = $this->getControlElement('fieldset', 'compare_products');
        for ($i = 1; $i <= $productCount; $i++) {
            foreach ($this->getChildElements($table, "//td[$i]") as $index => $cellData) {
                if ($names[$index] == 'remove') {
                    continue;
                }
                if ($names[$index] == 'product_name') {
                    $name = trim($this->getChildElement($cellData, 'strong')->text());
                    $data['product_' . $i][$names[$index]] = $name;
                    $this->addParameter('productName', $name);
                    $data['product_' . $i]['product_prices'] = $this->productHelper()->getFrontendProductPrices();
                } else {
                    $data['product_' . $i][$names[$index]] = $cellData->text();
                }
            }
        }
        return $data;
    }

    /**
     * Compare provided products data with actual info in Compare Products pop-up
     *
     * Preconditions: Compare Products pop-up is opened and selected
     *
     * @param array $verifyData Array of products info to be checked
     *
     * @return array Array of  error messages if any
     */
    public function frontVerifyProductDataInComparePopup($verifyData)
    {
        $actualData = $this->getProductDetailsOnComparePage();
        $this->assertEquals($verifyData, $actualData);
    }

    /**
     * Get list of available product attributes in Compare Products pop-up
     *
     * Preconditions: Compare Products pop-up is opened
     *
     * @return array $attributesList Array of available product attributes in Compare Products pop-up
     */
    public function frontGetAttributesListComparePopup()
    {
        $attributesList = array();
        $index = 1;
        /** @var $element PHPUnit_Extensions_Selenium2TestCase_Element */
        foreach ($this->getControlElements('pageelement', 'product_attribute_names') as $element) {
            $attributesList[trim($element->text())] = $index++;
        }
        return $attributesList;
    }

    /**
     * Get list of available products in Compare Products pop-up
     * Preconditions: Compare Products pop-up is opened
     *
     * @return array
     */
    public function frontGetProductsListComparePopup()
    {
        $productsList = array();
        $index = 1;
        /** @var $element PHPUnit_Extensions_Selenium2TestCase_Element */
        foreach ($this->getControlElements('pageelement', 'product_names') as $element) {
            $productsList[trim($element->text())] = $index++;
        }
        return $productsList;
    }
}