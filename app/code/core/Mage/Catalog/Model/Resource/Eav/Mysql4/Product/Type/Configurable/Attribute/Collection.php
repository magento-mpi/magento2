<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog super product attribute collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Type_Configurable_Attribute_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_labelTable;
    protected $_priceTable;
    protected $_product;

    protected function _construct()
    {
        $this->_init('catalog/product_type_configurable_attribute');
        $this->_labelTable = $this->getTable('catalog/product_super_attribute_label');
        $this->_priceTable = $this->getTable('catalog/product_super_attribute_pricing');
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        return $this;
    }

    public function setProductFilter($product)
    {
        $this->_product = $product;
        $this->addFieldToFilter('product_id', $product->getId());
        return $this;
    }

    public function getStoreId()
    {
        return (int) $this->_product->getStoreId();
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        $this->_loadLabels();
        $this->_loadPrices();
        return $this;
    }

    /**
     * Load attribute labels
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Type_Configurable_Attribute_Collection
     */
    protected function _loadLabels()
    {
        if ($this->count()) {
            $select = $this->getConnection()->select()
                ->from(array('default'=>$this->_labelTable))
                ->joinLeft(
                    array('store'=>$this->_labelTable),
                    'store.product_super_attribute_id=default.product_super_attribute_id AND store.store_id='.$this->getStoreId(),
                    array(
                        'store_lebel'=>'value',
                        'label' => new Zend_Db_Expr('IFNULL(store.value, default.value)')
                    )
                )
                ->where('default.product_super_attribute_id IN (?)', array_keys($this->_items))
                ->where('default.store_id=0');
                foreach ($this->getConnection()->fetchAll($select) as $data) {
                	$this->getItemById($data['product_super_attribute_id'])->setLabel($data['label']);
                }
        }
        return $this;
    }

    /**
     * Load attribute prices information
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Type_Configurable_Attribute_Collection
     */
    protected function _loadPrices()
    {
        if ($this->count()) {
            $select = $this->getConnection()->select()
                ->from(array('price'=>$this->_priceTable))
                ->join(array('option'=>$this->getTable('eav/attribute_option')),
                    'option.option_id=price.value_index'
                )
                ->joinLeft(array('option_label'=>$this->getTable('eav/attribute_option_value')),
                    'option_label.option_id=price.value_index AND option_label.store_id=' . $this->getStoreId(),
                    array('store_label'=>'value')
                )
                ->join(array('option_default_label'=>$this->getTable('eav/attribute_option_value')),
                    'option_default_label.option_id=price.value_index',
                    array(
                        'default_label'=>'value',
                        'label' => new Zend_Db_Expr('IFNULL(option_label.value, option_default_label.value)')
                    )
                )
                ->where('price.product_super_attribute_id IN (?)', array_keys($this->_items))
                ->where('option_default_label.store_id=0')
                ->order('option.sort_order asc');
            foreach ($this->getConnection()->fetchAll($select) as $data) {
                $this->getItemById($data['product_super_attribute_id'])->addPrice($data);
            }
        }
        return $this;
    }
}