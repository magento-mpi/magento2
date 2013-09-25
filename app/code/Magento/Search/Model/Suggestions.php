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
     * @var Magento_Search_Model_Search_Layer
     */
    protected $_searchLayer;

    /**
     * @param Magento_Search_Model_Search_Layer $searchLayer
     */
    function __construct(
        Magento_Search_Model_Search_Layer $searchLayer
    ) {
        $this->_searchLayer = $searchLayer;
    }

    /**
     * Retrieve search suggestions
     *
     * @return array
     */
    public function getSearchSuggestions()
    {
        return $this->_searchLayer->getProductCollection()->getSuggestionsData();
    }
}
