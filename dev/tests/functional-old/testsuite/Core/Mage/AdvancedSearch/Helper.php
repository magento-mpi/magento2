<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_AdvancedSearch_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Do Advanced Search
     *
     * @param array $productData
     */
    public function frontCatalogAdvancedSearch(array $productData = array())
    {
        if ($productData) {
            $this->fillFieldset($productData, 'advanced_search_information');
        }
        $this->formAdvancedSearchUrlParameter();
        $waitCondition = array(
            $this->_getMessageXpath('general_error'),
            $this->_getMessageXpath('general_validation'),
            $this->_getControlXpath('fieldset', 'search_result',
                $this->getUimapPage('frontend', 'advanced_search_result'))
        );
        $this->clickButton('search', false);
        $this->waitForElementVisible($waitCondition);
        $this->validatePage();
    }

    /**
     * Form Url data for Advanced Search
     */
    public function formAdvancedSearchUrlParameter()
    {
        $paramData = array();
        $fieldsetElement = $this->getControlElement('fieldset', 'advanced_search_information');
        /** @var $element PHPUnit_Extensions_Selenium2TestCase_Element */
        foreach ($this->getChildElements($fieldsetElement, '//input', false) as $element) {
            $paramData[$element->attribute('name')] = $element->attribute('value');
        }
        foreach ($this->getChildElements($fieldsetElement, '//select', false) as $element) {
            $paramData[$element->attribute('name')] = $element->attribute('value');
        }
        $this->addParameter('searchResult', http_build_query($paramData));
    }
}