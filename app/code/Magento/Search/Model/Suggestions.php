<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Enterprise search suggestions model
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Search_Model_Suggestions
{
    /**
     * Retrieve search suggestions
     *
     * @return array
     */
    public function getSearchSuggestions()
    {
        return Mage::getSingleton('Magento_Search_Model_Search_Layer')
            ->getProductCollection()
            ->getSuggestionsData();
    }
}
