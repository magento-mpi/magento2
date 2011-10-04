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
class MyAccount_Helper extends Mage_Selenium_TestCase
{
    /**
     * Validates product information in order review
     *
     * @param array|string $productsInfo
     */
    public function frontValidateProduct($productsInfo)
    {
        if (is_string($productsInfo)) {
            $productsInfo = $this->loadData($productsInfo);
        }
        $productsInfo = $this->arrayEmptyClear($productsInfo);
        $loginInfo = (isset($productsInfo['login'])) ? $productsInfo['login'] : NULL;
        $orderId = (isset($productsInfo['order_id'])) ? $productsInfo['order_id'] : NULL;
        $verificationData = (isset($productsInfo['validate'])) ? $productsInfo['validate'] : NULL;

        if ($loginInfo && $orderId) {
            $this->frontNavigateToMyOrders($loginInfo, $orderId);
        }
        $this->verifyPrices($verificationData);
    }

    /**
     * Navigate to My Account / My Orders
     *
     * @param array|string $loginInfo
     * @param string $orderId
     */
    public function frontNavigateToMyOrders($loginInfo, $orderId)
    {
        if (is_string($loginInfo)) {
            $loginInfo = $this->loadData($loginInfo);
        }
        $this->logoutCustomer();
        $this->clickControl('link', 'log_in');
        $this->fillForm($loginInfo);
        $this->clickButton('login');
        $this->frontend('my_orders_history');
        $this->frontViewOrder($orderId);
    }

    /**
     * Openes order view in My Account page
     *
     * @param string $orderId
     */
    public function frontViewOrder($orderId)
    {
        $this->addParameter('orderId', $orderId);
        $this->getUimapPage('frontend', 'my_orders_history')->assignParams($this->_paramsHelper);
        $this->frontend('my_orders_history');
        $xpathViewOrder = $this->_getControlXpath('link', 'view_order');
        $xpathNext = $this->_getControlXpath('link', 'next_page');
        $i = 1;
        for (;;) {
            if ($this->isElementPresent($xpathViewOrder)) {
                $this->clickControl('link', 'view_order');
                return TRUE;
            } else {
                if ($this->isElementPresent($xpathNext)) {
                    $i++;
                    $this->addParameter('param', '?p=' . $i);
                    $this->getUimapPage('frontend', 'my_orders_history_index')->assignParams($this->_paramsHelper);
                    $this->navigate('my_orders_history_index');
                } else {
                    $this->fail('Could not get the view link for order #' . $orderId);
                }
            }
        }
    }

    /**
     * Verifies the correctness of prices in the order
     *
     * @param array $verificationData
     */
    public function verifyPrices(array $verificationData)
    {
        foreach ($verificationData as $validate => $data) {
            if (preg_match('/^product_/', $validate)) {
                $this->verifyProductPrices($verificationData[$validate]);
            } else {
                $this->verifyTotalPrices($verificationData[$validate]);
            }
        }
        if (!empty($this->messages['error'])) {
            $this->fail(implode("\n", $this->messages['error']));
        }
    }

    /**
     * Verifies the prices in product row
     *
     * @param array $verificationData
     */
    public function verifyProductPrices(array $verificationData)
    {
        $page = $this->getCurrentLocationUimapPage();
        $pageelements = get_object_vars($page->getAllPageelements());
        $this->addParameter('productName', $verificationData['product_name']);
        foreach ($verificationData['verification'] as $key => $value) {
            if (preg_match('/price/', $key)) {
                $colIndex = $this->frontFindColumnNumberByName('Price');
            }
            if (preg_match('/subtotal/', $key)) {
                $colIndex = $this->frontFindColumnNumberByName('Subtotal');
            }
            if (preg_match('/qty/', $key)) {
                $colIndex = $this->frontFindColumnNumberByName('Qty');
            }
            $this->addParameter('price', $value);
            $this->addParameter('colIndex', $colIndex);
            $xpathPrice = $this->getCurrentLocationUimapPage()->getMainForm()->findPageelement($key);
            if (!$this->isElementPresent($xpathPrice)) {
                $this->messages['error']['price'] = 'Could not find element ' . $key . ' with price ' . $value;
            }
            unset($pageelements['ex_p_' . $key]);
        }
        foreach ($pageelements as $key => $value) {
            if (preg_match('/^ex_p_/', $key)) {
                if (preg_match('/price/', $key)) {
                    $colIndex = $this->frontFindColumnNumberByName('Price');
                }
                if (preg_match('/subtotal/', $key)) {
                    $colIndex = $this->frontFindColumnNumberByName('Subtotal');
                }
                if (preg_match('/qty/', $key)) {
                    $colIndex = $this->frontFindColumnNumberByName('Qty');
                }
                $value = preg_replace('/\%productName\%/', $verificationData['product_name'], $value);
                $value = preg_replace('/\%colIndex\%/', $colIndex, $value);
                if ($this->isElementPresent($value)) {
                    $this->messages['error']['price'] = 'Element ' . $key . ' is on the page';
                }
            }
        }
        return $this->messages['error'];
    }

    /**
     * Verifies the prices in total row
     *
     * @param array $verificationData
     */
    public function verifyTotalPrices(array $verificationData)
    {
        $page = $this->getCurrentLocationUimapPage();
        $pageelements = get_object_vars($page->getAllPageelements());
        foreach ($verificationData as $key => $value) {
            $this->addParameter('price', $value);
            $xpathPrice = $this->getCurrentLocationUimapPage()->getMainForm()->findPageelement($key);
            if (!$this->isElementPresent($xpathPrice)) {
                $this->messages['error']['total'] = 'Could not find element ' . $key . ' with price ' . $value;
            }
            unset($pageelements['ex_t_' . $key]);
        }
        foreach ($pageelements as $key => $value) {
            if (preg_match('/^ex_t_/', $key)) {
                if ($this->isElementPresent($value)) {
                    $this->messages['error']['total'] = 'Element ' . $key . ' is on the page';
                }
            }
        }
        return $this->messages['error'];
    }

    /**
     * Find Column Number in table by Name
     *
     * @param type $columnName
     * @return int
     */
    public function frontFindColumnNumberByName($columnName)
    {
        $columnXpath = "//table[@class='data-table']//thead/tr/th";
        $columnQty = $this->getXpathCount($columnXpath);
        for ($i = 1; $i <= $columnQty; $i++) {
            $text = $this->getText($columnXpath . "[$i]");
            if ($text == $columnName) {
                return $i;
            }
        }
        return 0;
    }
}
