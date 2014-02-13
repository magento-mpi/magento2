<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Search;

/**
 * Search Catalog Model
 */
class Catalog extends \Magento\Object
{
    /**
     * Catalog search data
     *
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $_catalogSearchData = null;

    /**
     * Magento string lib
     *
     * @var \Magento\Stdlib\String
     */
    protected $string;

    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_adminhtmlData = null;

    /**
     * @param \Magento\Backend\Helper\Data $adminhtmlData
     * @param \Magento\Stdlib\String $string
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     */
    public function __construct(
        \Magento\Backend\Helper\Data $adminhtmlData,
        \Magento\Stdlib\String $string,
        \Magento\CatalogSearch\Helper\Data $catalogSearchData
    ) {
        $this->_adminhtmlData = $adminhtmlData;
        $this->string = $string;
        $this->_catalogSearchData = $catalogSearchData;
    }

    /**
     * Load search results
     *
     * @return $this
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
                'description'   => $this->string->substr($description, 0, 30),
                'url' => $this->_adminhtmlData->getUrl('catalog/product/edit', array('id' => $product->getId())),
            );
        }

        $this->setResults($result);

        return $this;
    }
}
