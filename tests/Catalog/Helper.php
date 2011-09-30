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
            $this->verifyPrices($verificationData, $storeName, $productName, $foundIt);
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
                    $this->addParameter('index', $i);
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
     * @return array
     */
    public function verifyPrices(array $verificationData, $storeName, $productName, $foundIt)
    {
        $this->addParameter('index', $foundIt);
        $this->addParameter('productName', $productName);
        $this->addParameter('storeName', $storeName);
        //$this->getUimapPage('frontend', 'catalog_page')->assignParams($this->_paramsHelper);
        $this->getUimapPage('frontend', 'catalog_page_index')->assignParams($this->_paramsHelper);
        $this->navigate('catalog_page_index');
        $mca = $this->getUimapPage('frontend', 'catalog_page_index')->getMca();
        print_r($mca);
        //$this->pleaseWait();
        $page = $this->getCurrentLocationUimapPage();
//        $formData = $page->getMainForm();
//        $fieldsets = $formData->getAllFieldsets();
//        print_r($fieldsets);
//        $pageelements = $fieldsets->getAllPageelements();
//        print_($pageelements);
        //$pricesMissmatch = array();
        //return $pricesMissmatch;
    }
}
