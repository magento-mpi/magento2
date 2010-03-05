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
 * Accordion grid for products in wishlist
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_Wishlist 
    extends Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_Abstract
{
    /**
     * Initialize Grid
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('wishlist_grid');
        $this->setDefaultSort('added_at');
        $this->setHeaderText(
            Mage::helper('enterprise_checkout')->__('Wishlist (%s)', $this->getItemsCount())
        );
    }

    public function getJsObjectName()
    {
        return 'wishlistItemsGrid';
    }
    
    /**
     * Return items collection
     *
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function getItemsCollection() 
    {
        if (!$this->hasData('items_collection')) {
            $wishlist = Mage::getModel('wishlist/wishlist');
            $collection = $wishlist->loadByCustomer($this->_getCustomer())
                ->setSharedStoreIds($wishlist->getSharedStoreIds(false))
                ->getProductCollection()
                    ->resetSortOrder()
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('price');
            $this->setData('items_collection', $collection);
        }
        return $this->_getData('items_collection');
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
            'filter'    => false,
        ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('sales')->__('Price'),
            'align'     => 'right',
            'type'      => 'price',
            'currency_code' => $this->_getStore()->getBaseCurrency()->getCode(),
            'index'     => 'price'
        ));

        $this->addColumn('in_products', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'field_name'=> 'add_product[]',
            'align'     => 'center',
            'index'     => 'entity_id',
        ));
        
        return parent::_prepareColumns();
    }

    /**
     * Return grid URL for sorting and filtering
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/viewWishlist', array('_current'=>true));
    }
}
