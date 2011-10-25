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
class Tags_Helper extends Mage_Selenium_TestCase
{

    /**
     * <p>Create Tag</p>
     *
     * @param string $tagName
     */
    public function frontendAddTag($tagName)
    {
        if (is_array($tagName) && array_key_exists('new_tag_names', $tagName)) {
            $tagName = $tagName['new_tag_names'];
        } else {
            $this->fail('Array key is absent in array');
        }
        $tagQty = count(explode(' ', $tagName));
        $this->addParameter('tagQty', $tagQty);
        $tagXpath = $this->_getControlXpath('field', 'input_new_tags');
        if (!$this->isElementPresent($tagXpath)) {
            $this->fail('Element is absent on the page');
        }
        $this->type($tagXpath, $tagName);
        $this->clickButton('add_tags');
    }

    /**
     * Verification tags on frontend
     *
     * @param array $verificationData
     */
    public function frontendTagVerification($verificationData)
    {
        if (is_array($verificationData) && array_key_exists('new_tag_names', $verificationData)) {
            $tagName = $verificationData['new_tag_names'];
        } else {
            $this->fail('Array key is absent in array');
        }
        if (array_key_exists('product_name', $verificationData)) {
            $productName = $verificationData['product_name'];
        } else {
            $this->fail('Array key is absent in array');
        }
        $this->navigate('customer_account');
        $this->addParameter('productName', $productName);


        $tagNameArray = explode(' ', $tagName);
        foreach ($tagNameArray as $value){
                $this->addParameter('tagName', $value);
                $xpath = $this->_getControlXpath('link', 'product_info');
                $this->assertTrue($this->isElementPresent($xpath), "Cannot find tag with name: $value");
        }
        $this->navigate('my_account_my_tags');
        foreach ($tagNameArray as $value) {
            $this->addParameter('tagName', $value);
            $xpath = $this->_getControlXpath('link', 'tag_name');
            $this->assertTrue($this->isElementPresent($xpath), "Cannot find tag with name: $value");

            $this->clickControl('link', 'tag_name');
            $xpath = $this->_getControlXpath('link', 'product_name');
            $this->assertTrue($this->isElementPresent($xpath), "Cannot find tag with name: $value");
            $xpath = $this->_getControlXpath('pageelement', 'tag_name_box');
            $this->assertTrue($this->isElementPresent($xpath), "Cannot find tag with name: $value");
            $this->clickControl('link', 'back_to_tags_list');
        }
    }
}
