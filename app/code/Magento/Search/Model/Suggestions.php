<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model;

 /**
 * Enterprise search suggestions model
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Suggestions
{
    /**
     * Search layer
     *
     * @var \Magento\Search\Model\Search\Layer
     */
    protected $_searchLayer;

    /**
     * @param \Magento\Search\Model\Search\Layer $searchLayer
     */
    function __construct(
        \Magento\Search\Model\Search\Layer $searchLayer
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
