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
 * @subpackage  Mage_Selenium
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

class Community17_Mage_LayeredNavigation_Helper extends Mage_Selenium_TestCase
{
    /**
     * Set category ID from link into UImap
     * @param string $categoryName
     */
    public function setCategoryIdFromLink($categoryName)
    {
        $this->addParameter('categoryName', $categoryName);
        $linkXpath = $this->_getControlXpath('link', 'category_name');
        $link = $this->getAttribute($linkXpath . '/@href');
        // parse link received from xpath
        $parsedLink = parse_url($link);
        parse_str($parsedLink['query']);
        if (isset($cat)) {
            $this->addParameter('catid', $cat);
        }
        else {
            fail("There is no category ID in the parsed link");
        }
    }

    /**
     * Set ID from link into UImap
     * @param string $attributeName
     * @param string $attributeCode
     * @param string $categoryName
     */
    public function setAttributeIdFromLink($categoryName, $attributeCode, $attributeName = null)
    {
        $this->addParameter('categoryName', $categoryName);
        $this->addParameter('attributeCode', $attributeCode);
        if (isset($attributeName)){
            $this->addParameter('attributeName', $attributeName);
            $linkXpath = $this->_getControlXpath('link', 'attribute_name');
        }
        else {
            $this->addParameter('priceAttributeCode', $attributeCode);
            $linkXpath = $this->_getControlXpath('link', 'price_attribute');
        }
        $link = $this->getAttribute($linkXpath . '/@href');
        // parse link received from xpath
        $parsedLink = parse_url($link);
        parse_str($parsedLink['query']);
        if (isset($$attributeCode)) {
            $this->addParameter('attributeId', $$attributeCode);
        }
        else {
            fail("There is no attribute ID in the parsed link");
        }
    }

    /**
     * Verify page elements which should appear after selecting attribute
     */
    public function verifyAfterSelectingAttribute()
    {
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'currently_shopping_by')),
            'There is no currently_shopping_by block in layerd navigation');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('button', 'remove_this_item')),
            'There is no "remove this item" button');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('link', 'clear_all')),
            'There is no "Clear All" link');
    }

    /**
     * Verify page elements which should appear after selecting attribute
     */
    public function verifyAfterRemovingAttribute()
    {
        $this->assertFalse($this->isElementPresent($this->_getControlXpath('button', 'remove_this_item')),
            'remove_this_item button still present in layered navigation block');
        $this->assertFalse($this->isElementPresent($this->_getControlXpath('link', 'clear_all')),
            '"Clear All" link still present in layered navigation block');
        $this->assertFalse($this->isElementPresent($this->_getControlXpath('pageelement', 'currently_shopping_by')),
            'currently_shopping_by block still present in layered navigation block');
    }

}