<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CatalogRule_Model_Resource_Rule_Product_Price_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento_CatalogRule_Model_Rule_Product_Price', 'Magento_CatalogRule_Model_Resource_Rule_Product_Price');
    }

    /**
     * @return array
     */
    public function getProductIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->columns('main_table.product_id');
        $idsSelect->distinct(true);
        return $this->getConnection()->fetchCol($idsSelect);
    }
}
