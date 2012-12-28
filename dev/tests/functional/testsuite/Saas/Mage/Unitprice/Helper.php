<?php

/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
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
class Saas_Mage_Unitprice_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Edit product and set Unit Price fields
     *
     * @param array $productData
     * @param array $unitPriceData
     */
    public function setProductUnitPrice($productData, $unitPriceData)
    {
        $this->productHelper()->openProduct($productData);
        $this->openTab('prices');
        $this->fillFieldset($unitPriceData, 'unit_price_fields');
        $this->saveForm('save');
    }

    /*
     * Verifies UnitPrice lalbel on product view page
     *
     * @param string $unitPriceLabel
     * @param string $productName
     * @param bool $isVisible
     */
    public function verifyUnitPriceOnProductPage($unitPriceLabel, $productName, $isVisible = true)
    {
        $this->productHelper()->frontOpenProduct($productName);
        $this->addParameter('prodName', $productName);

        $labelXpath = $this->_getControlXpath('pageelement', 'unit_price_label');
        if ($isVisible) {
            $text = implode ($this->getElementsValue($labelXpath, 'text'));
            $this->assertEquals($unitPriceLabel, $text);
        } else {
            $this->assertFalse(
                $this->elementIsPresent($labelXpath),
                'Unit price should be invisible for product ' . $productName
            );
        }
    }

    /*
     * Verifies UnitPrice label on category view page
     *
     * @param string $unitPriceLabel
     * @param string $categoryPath
     * @param string $productName
     * @param bool $isVisible
     */
    public function verifyUnitPriceOnCategoryPage($unitPriceLabel, $categoryPath, $productName, $isVisible = true)
    {
        $this->categoryHelper()->frontOpenCategory($categoryPath);

        //Wait for product
        $this->productHelper()->isProductPresent($productName);

        $this->addParameter('prodName', $productName);

        if ($isVisible) {
            $text = $this->getElementsValue($this->_getControlXpath('pageelement', 'unit_price_label_by_product_name'), 'text');
            $this->assertEquals($unitPriceLabel, $text[0]);
        } else {
            $xPath = $this->_getControlXpath('pageelement', 'unit_price_label_by_product_name');
            $this->assertFalse(
                $this->elementIsPresent($xPath),
                'Unit price should be invisible for product ' . $productName
            );
        }
    }
}
