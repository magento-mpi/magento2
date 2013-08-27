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
class Magento_Adminhtml_Model_Search_Catalog extends Magento_Object
{
    /**
     * Catalog search data
     *
     * @var Magento_CatalogSearch_Helper_Data
     */
    protected $_catalogSearchData = null;

    /**
     * Core string
     *
     * @var Magento_Core_Helper_String
     */
    protected $_coreString = null;

    /**
     * Adminhtml data
     *
     * @var Magento_Adminhtml_Helper_Data
     */
    protected $_adminhtmlData = null;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param Magento_Adminhtml_Helper_Data $adminhtmlData
     * @param Magento_Core_Helper_String $coreString
     * @param Magento_CatalogSearch_Helper_Data $catalogSearchData
     */
    public function __construct(
        Magento_Adminhtml_Helper_Data $adminhtmlData,
        Magento_Core_Helper_String $coreString,
        Magento_CatalogSearch_Helper_Data $catalogSearchData
    ) {
        $this->_adminhtmlData = $adminhtmlData;
        $this->_coreString = $coreString;
        $this->_catalogSearchData = $catalogSearchData;
    }

    /**
     * Load search results
     *
     * @return Magento_Adminhtml_Model_Search_Catalog
     */
    public function load()
    {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
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
            $arr[] = array(
                'id'            => 'product/1/'.$product->getId(),
                'type'          => __('Product'),
                'name'          => $product->getName(),
                'description'   => $this->_coreString->substr($description, 0, 30),
                'url' => $this->_adminhtmlData->getUrl(
                    '*/catalog_product/edit',
                    array(
                        'id' => $product->getId()
                    )
                ),
            );
        }

        $this->setResults($arr);

        return $this;
    }
}
