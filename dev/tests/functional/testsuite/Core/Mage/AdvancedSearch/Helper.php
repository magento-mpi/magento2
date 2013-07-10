<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
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
class Core_Mage_AdvancedSearch_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Do Advanced Search
     *
     * @param array $productData
     */
    public function frontCatalogAdvancedSearch(array $productData)
    {
        $this->fillFieldset($productData, 'advanced_search_information');
        $this->formAdvancedSearchUrlParameter();
        $this->saveForm('search');
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