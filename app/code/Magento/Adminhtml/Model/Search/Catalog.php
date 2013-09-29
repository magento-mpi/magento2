<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Search Catalog Model
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Model\Search;

class Catalog extends \Magento\Object
{
    /**
     * Catalog search data
     *
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $_catalogSearchData = null;

    /**
     * Core string
     *
     * @var \Magento\Core\Helper\String
     */
    protected $_coreString = null;

    /**
     * Adminhtml data
     *
     * @var \Magento\Adminhtml\Helper\Data
     */
    protected $_adminhtmlData = null;

    /**
     * @param \Magento\Adminhtml\Helper\Data $adminhtmlData
     * @param \Magento\Core\Helper\String $coreString
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     */
    public function __construct(
        \Magento\Adminhtml\Helper\Data $adminhtmlData,
        \Magento\Core\Helper\String $coreString,
        \Magento\CatalogSearch\Helper\Data $catalogSearchData
    ) {
        $this->_adminhtmlData = $adminhtmlData;
        $this->_coreString = $coreString;
        $this->_catalogSearchData = $catalogSearchData;
    }

    /**
     * Load search results
     *
     * @return \Magento\Adminhtml\Model\Search\Catalog
     */
    public function load()
    {
        $result = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($result);
            return $this;
        }

        $collection = $this->_catalogSearchData->getQuery()->getSearchCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('description')
            ->addSearchFilter($this->getQuery())
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();

        foreach ($collection as $product) {
            $description = strip_tags($product->getDescription());
            $result[] = array(
                'id'            => 'product/1/'.$product->getId(),
                'type'          => __('Product'),
                'name'          => $product->getName(),
                'description'   => $this->_coreString->substr($description, 0, 30),
                'url' => $this->_adminhtmlData->getUrl('*/catalog_product/edit', array('id' => $product->getId())),
            );
        }

        $this->setResults($result);

        return $this;
    }
}
