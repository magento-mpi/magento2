<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Bundle Resource Model
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Model_Resource_Bundle extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * @var Magento_Catalog_Model_Resource_Product_Relation
     */
    protected $_productRelation;

    /**
     * Class constructor
     *
     * @param Magento_Catalog_Model_Resource_Product_Relation $productRelation
     * @param Magento_Core_Model_Resource $resource
     */
    public function __construct(
        Magento_Catalog_Model_Resource_Product_Relation $productRelation,
        Magento_Core_Model_Resource $resource
    ) {
        parent::__construct($resource);
        $this->_productRelation = $productRelation;
    }

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('catalog_product_entity', 'entity_id');
    }

    /**
     * Preparing select for getting selection's raw data by product id
     * also can be specified extra parameter for limit which columns should be selected
     *
     * @param int $productId
     * @param array $columns
     * @return Zend_DB_Select
     */
    protected function _getSelect($productId, $columns = array())
    {
        return $this->_getReadAdapter()->select()
            ->from(array("bundle_option" => $this->getTable('catalog_product_bundle_option')), array('type', 'option_id'))
            ->where("bundle_option.parent_id = ?", $productId)
            ->where("bundle_option.required = 1")
            ->joinLeft(array(
                "bundle_selection" => $this->getTable('catalog_product_bundle_selection')),
                "bundle_selection.option_id = bundle_option.option_id",
                $columns
            );
    }

    /**
     * Retrieve selection data for specified product id
     *
     * @param int $productId
     * @return array
     */
    public function getSelectionsData($productId)
    {
        return $this->_getReadAdapter()->fetchAll($this->_getSelect(
            $productId,
            array("*")
        ));
    }

    /**
     * Removing all quote items for specified product
     *
     * @param int $productId
     */
    public function dropAllQuoteChildItems($productId)
    {
        $quoteItemIds = $this->_getReadAdapter()->fetchCol(
            $this->_getReadAdapter()->select()
            ->from($this->getTable('sales_flat_quote_item'), array('item_id'))
            ->where('product_id = :product_id'),
            array('product_id' => $productId)
        );

        if ($quoteItemIds) {
            $this->_getWriteAdapter()->delete(
                $this->getTable('sales_flat_quote_item'),
                array('parent_item_id IN(?)' => $quoteItemIds)
            );
        }
    }

    /**
     * Removes specified selections by ids for specified product id
     *
     * @param int $productId
     * @param array $ids
     */
    public function dropAllUnneededSelections($productId, $ids)
    {
        $where = array(
            'parent_product_id = ?' => $productId
        );
        if (!empty($ids)) {
            $where['selection_id NOT IN (?) '] = $ids;
        }
        $this->_getWriteAdapter()
            ->delete($this->getTable('catalog_product_bundle_selection'), $where);
    }

    /**
     * Save product relations
     *
     * @param int $parentId
     * @param array $childIds
     * @return Magento_Bundle_Model_Resource_Bundle
     */
    public function saveProductRelations($parentId, $childIds)
    {
        $this->_productRelation->processRelations($parentId, $childIds);

        return $this;
    }

}
