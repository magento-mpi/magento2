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
class Catalog_Helper extends Mage_Selenium_TestCase
{
    /**
     * Validates product information in catalog
     *
     * @param array|string $productsInfo
     * @return bool
     */
    public function validateProduct($productsInfo)
    {
        $productsInfo = $this->arrayEmptyClear($productsInfo);
        $storeName = (isset($productsInfo['store_name'])) ? $productsInfo['store_name'] : NULL;
        $productName = (isset($productsInfo['product_name'])) ? $productsInfo['product_name'] : NULL;
        $verificationData = (isset($productsInfo['verification'])) ? $productsInfo['verification'] : NULL;
        if ($storeName != NULL && $productName != NULL) {
            $foundIt = $this->searchPageWithProduct($productName, $storeName);
            if (!$foundIt) {
                $this->fail('Could not find the product');
            }
        }
        if ($verificationData) {
            $this->verifyPrices($verificationData, $foundIt);
        }
    }

    /**
     * Validates product information in catalog
     *
     * @param string $productName
     * @param string $storeName
     * @return mixed
     */
    public function searchPageWithProduct($productName, $storeName)
    {
        $this->addParameter('storeName', $storeName);
        $this->getUimapPage('frontend', 'catalog_page')->assignParams($this->_paramsHelper);
        $this->frontend('catalog_page');
        $xpathNext = $this->_getControlXpath('link', 'next_page');
        $this->addParameter('productName', $productName);
        $xpathProduct = $this->_getControlXpath('link', 'product_name');
        $foundIt = FALSE;
        $i = 1;
        do {
            if ($this->isElementPresent($xpathProduct)) {
                return $i;
            } else {
                if ($this->isElementPresent($xpathNext)) {
                    $i++;
                    $this->addParameter('param', '?p=' . $i);
                    $this->getUimapPage('frontend', 'catalog_page_index')->assignParams($this->_paramsHelper);
                    $this->navigate('catalog_page_index');
                } else {
                    return $foundIt;
                }
            }
        } while (!$foundIt);
        return $i;
    }

    /**
     * Verifies the correctness of prices in the catalog
     *
     * @param array $verificationData
     * @param int $pageNum
     */
    public function verifyPrices(array $verificationData, $pageNum)
    {
        $this->addParameter('param', '?p=' . $pageNum);
        $this->getUimapPage('frontend', 'catalog_page_index')->assignParams($this->_paramsHelper);
        $page = $this->getCurrentLocationUimapPage();
        $xpathProduct = $this->_getControlXpath('link', 'product_name');
        $this->addParameter('productNameXpath', $xpathProduct);
        $pageelements = get_object_vars($page->getAllPageelements());
        foreach ($verificationData as $key => $value) {
            $this->addParameter('price', $value);
            $xpathPrice = $this->getCurrentLocationUimapPage()->getMainForm()->findPageelement($key);
            $this->assertTrue($this->isElementPresent($xpathPrice),
                    'Could not find element ' . $key . ' with price ' . $value);
            unset($pageelements['ex_' . $key]);
        }
        foreach ($pageelements as $key => $value) {
            if (!preg_match('/^ex_/', $key)) {
                unset ($pageelements[$key]);
            } else {
                $value = preg_replace('/\%productNameXpath\%/', $xpathProduct, $value);
                $this->assertFalse($this->isElementPresent($value), 'Element ' . $key . ' is on the page');
            }
        }
    }
}
