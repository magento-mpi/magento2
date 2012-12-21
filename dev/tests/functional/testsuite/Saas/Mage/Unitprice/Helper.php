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
 * @author      Magento Goext Team <DL-Magento-Team-Goext@corp.ebay.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
