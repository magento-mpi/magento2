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
class Community2_Mage_AdvancedSearch_Helper extends Mage_Selenium_TestCase
{
    /**
     * Do Advanced Search
     * @param $productData
     */
    public function frontCatalogAdvancedSearch($productData)
    {
        $searchFormFieldMap = array(
            'name' => 'name',
            'description' => 'description',
            'short_description' => 'short_description',
            'sku' => 'sku',
            'price[from]' => 'price_from',
            'price[to]' => 'price_to',
        );
        $searchString = '';
        foreach ($searchFormFieldMap as $fieldName => $attributeName) {
            $attributeValue = !empty($productData[$attributeName]) ? $productData[$attributeName] : '';
            $searchString .= '&' . urlencode($fieldName) . '=' . urlencode($attributeValue);
        }
        if (!empty($searchString)) {
            $searchString = substr($searchString, 1);
        }
        $this->addParameter('searchResult', $searchString);
        $this->fillFieldset($productData, 'advanced_search_information');
    }
}