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
class Magento_Catalog_Model_Resource_Product_Type_Configurable_Product_Collection
    extends Magento_Catalog_Model_Resource_Product_Collection
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
     * @return Magento_Catalog_Model_Resource_Product_Type_Configurable_Product_Collection
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
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_Catalog_Model_Resource_Product_Type_Configurable_Product_Collection
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
