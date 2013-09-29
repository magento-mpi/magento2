<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog super product link collection
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Resource\Product\Type\Configurable\Product;

class Collection
    extends \Magento\Catalog\Model\Resource\Product\Collection
{
    /**
     * Link table name
     *
     * @var string
     */
    protected $_linkTable;

    /**
     * Assign link table name
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_linkTable = $this->getTable('catalog_product_super_link');
    }

    /**
     * Init select
     * @return \Magento\Catalog\Model\Resource\Product\Type\Configurable\Product\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->join(array('link_table' => $this->_linkTable),
            'link_table.product_id = e.entity_id',
            array('parent_id')
        );

        return $this;
    }

    /**
     * Set Product filter to result
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Resource\Product\Type\Configurable\Product\Collection
     */
    public function setProductFilter($product)
    {
        $this->getSelect()->where('link_table.parent_id = ?', (int) $product->getId());
        return $this;
    }

    /**
     * Retrieve is flat enabled flag
     * Return alvays false if magento run admin
     *
     * @return bool
     */
    public function isEnabledFlat()
    {
        return false;
    }
}
