<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model;

/**
 * Enterprise search suggestions model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Suggestions
{
    /**
     * Search layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_searchLayer;

    /**
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Resolver $layerResolver
    ) {
        $this->_searchLayer = $layerResolver->get();
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
