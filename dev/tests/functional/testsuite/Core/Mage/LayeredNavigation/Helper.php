<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_LayeredNavigation
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
class Core_Mage_LayeredNavigation_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Set category ID from link into UIMap
     *
     * @param string $categoryName
     */
    public function setCategoryIdFromLink($categoryName)
    {
        $this->addParameter('categoryName', $categoryName);
        $link = $this->getControlAttribute('link', 'category_name', 'href');
        // parse link received from xpath
        $parsedLink = parse_url($link);
        parse_str($parsedLink['query']);
        if (isset($cat)) {
            $this->addParameter('catid', $cat);
        } else {
            $this->fail("There is no category ID in the parsed link");
        }
    }

    /**
     * Set ID from link into UIMap
     *
     * @param string $attributeName
     * @param string $attributeCode
     * @param string $categoryName
     */
    public function setAttributeIdFromLink($categoryName, $attributeCode, $attributeName = null)
    {
        $this->addParameter('categoryName', $categoryName);
        $this->addParameter('attributeCode', $attributeCode);
        if (isset($attributeName)) {
            $this->addParameter('attributeName', $attributeName);
            $link = $this->getControlAttribute('link', 'attribute_name', 'href');
        } else {
            $this->addParameter('priceAttributeCode', $attributeCode);
            $link = $this->getControlAttribute('link', 'price_attribute', 'href');
        }
        // parse link received from xpath
        $parsedLink = parse_url($link);
        parse_str($parsedLink['query']);
        if (isset($$attributeCode)) {
            $this->addParameter('attributeId', $$attributeCode);
        } else {
            $this->fail("There is no attribute ID in the parsed link");
        }
    }

    /**
     * Verify page elements which should appear after selecting attribute
     */
    public function frontVerifyAfterSelectingAttribute()
    {
        $this->assertTrue($this->controlIsPresent('pageelement', 'currently_shopping_by'),
            'There is no currently_shopping_by block in layered navigation');
        $this->assertTrue($this->controlIsPresent('button', 'remove_this_item'),
            'There is no "remove this item" button');
        $this->assertTrue($this->controlIsPresent('link', 'clear_all'), 'There is no "Clear All" link');
    }

    /**
     * Verify page elements which should appear after selecting attribute
     */
    public function frontVerifyAfterRemovingAttribute()
    {
        $this->assertFalse($this->controlIsPresent('button', 'remove_this_item'),
            'remove_this_item button still present in layered navigation block');
        $this->assertFalse($this->controlIsPresent('link', 'clear_all'),
            '"Clear All" link still present in layered navigation block');
        $this->assertFalse($this->controlIsPresent('pageelement', 'currently_shopping_by'),
            'currently_shopping_by block still present in layered navigation block');
    }
}
