<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Block\Catalogsearch;
use Magento\Search\Model\Layer\Category\FilterList;
use Magento\LayeredNavigation\Block\Navigation;
use Magento\CatalogSearch\Model\Resource\EngineProvider;
use Magento\CatalogSearch\Helper\Data;
use Magento\CatalogSearch\Model\Layer as ModelLayer;
use Magento\Registry;
use Magento\View\Element\Template\Context;

/**
  * Layered Navigation block for search
  *
  * @category    Magento
  * @package     Magento_Search
  * @author      Magento Core Team <core@magentocommerce.com>
  */
class Layer extends \Magento\CatalogSearch\Block\Layer
{
    /**
     * Search data
     *
     * @var \Magento\Search\Helper\Data
     */
    protected $_searchData;

    public function __construct(
        Context $context,
        ModelLayer $catalogLayer,
        FilterList $filterList,
        EngineProvider $engineProvider,
        Data $catalogSearchData,
        Registry $registry,
        \Magento\Search\Helper\Data $searchData,
        array $data = array()
    ) {
        $this->_searchData = $searchData;
        parent::__construct(
            $context, $catalogLayer, $filterList, $engineProvider, $catalogSearchData, $registry, $data
        );
    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock()
    {
        if ($this->_searchData->isThirdPartSearchEngine() && $this->_searchData->isActiveEngine()) {
            return ($this->canShowOptions() || count($this->getLayer()->getState()->getFilters()));
        }
        return parent::canShowBlock();
    }

    /**
     * Get layer object
     *
     * @return \Magento\Catalog\Model\Layer
     */
    public function getLayer()
    {
        if ($this->_searchData->isThirdPartSearchEngine() && $this->_searchData->isActiveEngine()) {
            return $this->_searchLayer;
        }

        return parent::getLayer();
    }
}
