<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Abstract class for accordion grids
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_Abstract extends Enterprise_Enterprise_Block_Adminhtml_Widget_Grid
{
    /**
     * Initialize Grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(true);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setRowClickCallback('checkoutObj.accordionGridRowClick.bind(checkoutObj)');
    }

    /**
     * Return items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        if ($collection = $this->getItemsCollection()) {
            return count($collection->getItems());
        }
        return 0;
    }
    
    /**
     * Return items collection
     *
     * @return Mage_Core_Model_Mysql4_Collection_Abstract|bool
     */
    public function getItemsCollection()
    {
        return false;
    }
    
    /**
     * Prepare collection for grid
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->getItemsCollection());
        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('product_name', array(
            'header'    => Mage::helper('customer')->__('Product name'),
            'index'     => 'name',
            'sortable'  => false
        ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('sales')->__('Price'),
            'align'     => 'right',
            'type'      => 'price',
            'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
            'index'     => 'price',
            'sortable'  => false
        ));

        $this->addColumn('in_products', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'field_name'=> 'add_product',
            'align'     => 'center',
            'index'     => 'entity_id',
        ));
        
        return parent::_prepareColumns();
    }
    
    /**
     * Return current customer from regisrty
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return Mage::registry('checkout_current_customer');
    }

    /**
     * Return current store from regisrty
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        return Mage::registry('checkout_current_store');
    }
    
    /**
     * Empty html when no items found
     *
     * @return string
     */
    protected function _toHtml() 
    {
        if ($this->getItemsCount()) {
            return parent::_toHtml();
        }
        return '';
    }
}
