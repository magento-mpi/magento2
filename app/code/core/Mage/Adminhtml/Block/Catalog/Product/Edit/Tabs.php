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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * admin product edit tabs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected $_attributeTabBlock = 'adminhtml/catalog_product_edit_tab_attributes';

    public function __construct()
    {
        parent::__construct();
        $this->setId('product_info_tabs');
        $this->setDestElementId('product_edit_form');
        $this->setTitle(Mage::helper('catalog')->__('Product Information'));
    }

    protected function _prepareLayout()
    {
        $product = $this->getProduct();

        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }

        if ($setId) {
            $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
                ->setAttributeSetFilter($setId)
                ->load();

            foreach ($groupCollection as $group) {
                $attributes = $product->getAttributes($group->getId(), true);
                // do not add grops without attributes

                foreach ($attributes as $key => $attribute) {
                    if( !$attribute->getIsVisible() ) {
                        unset($attributes[$key]);
                    }
                }

                if (count($attributes)==0) {
                    continue;
                }

                $this->addTab('group_'.$group->getId(), array(
                    'label'     => Mage::helper('catalog')->__($group->getAttributeGroupName()),
                    'content'   => $this->getLayout()->createBlock($this->getAttributeTabBlock())
                        ->setGroup($group)
                        ->setGroupAttributes($attributes)
                        ->toHtml(),
                ));
            }

            $this->addTab('inventory', array(
                'label'     => Mage::helper('catalog')->__('Inventory'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_inventory')->toHtml(),
            ));


            /**
             * Don't display website tab for single mode
             */
            if (!Mage::app()->isSingleStoreMode()) {
                $this->addTab('websites', array(
                    'label'     => Mage::helper('catalog')->__('Websites'),
                    'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_websites')->toHtml(),
                ));
            }

            $this->addTab('categories', array(
                'label'     => Mage::helper('catalog')->__('Categories'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_categories')->toHtml(),
            ));

            $this->addTab('related', array(
                'label'     => Mage::helper('catalog')->__('Related Products'),
                'url'       => $this->getUrl('*/*/related', array('_current' => true)),
                'class'     => 'ajax',
            ));

            $this->addTab('upsell', array(
                'label'     => Mage::helper('catalog')->__('Up-sells'),
                'url'       => $this->getUrl('*/*/upsell', array('_current' => true)),
                'class'     => 'ajax',
            ));

            $this->addTab('crosssell', array(
                'label'     => Mage::helper('catalog')->__('Cross-sells'),
                'url'       => $this->getUrl('*/*/crosssell', array('_current' => true)),
                'class'     => 'ajax',
            ));

            $storeId = 0;
            if ($this->getRequest()->getParam('store')) {
                $storeId = Mage::app()->getStore($this->getRequest()->getParam('store'))->getId();
            }

            $alertPriceAllow = Mage::getStoreConfig('catalog/productalert/allow_price');
            $alertStockAllow = Mage::getStoreConfig('catalog/productalert/allow_stock');

            if ($alertPriceAllow || $alertStockAllow) {
                $this->addTab('productalert', array(
                    'label'     => Mage::helper('catalog')->__('Product Alerts'),
                    'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_alerts', 'admin.alerts.products')->toHtml()
                ));
            }

            if( $this->getRequest()->getParam('id', false) ) {
                $this->addTab('reviews', array(
                    'label' => Mage::helper('catalog')->__('Product Reviews'),
                    'url'   => $this->getUrl('*/*/reviews', array('_current' => true)),
                    'class' => 'ajax',
                ));

                $this->addTab('tags', array(
                    'label'     => Mage::helper('catalog')->__('Product Tags'),
                    'url'   => $this->getUrl('*/*/tagGrid', array('_current' => true)),
                    'class' => 'ajax',
                ));

                $this->addTab('customers_tags', array(
                    'label'     => Mage::helper('catalog')->__('Customers Tagged Product'),
                    'url'   => $this->getUrl('*/*/tagCustomerGrid', array('_current' => true)),
                    'class' => 'ajax',
                ));
            }

            $this->addTab('customer_options', array(
                'label' => Mage::helper('catalog')->__('Customer Defined Options'),
                'content' => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_options', 'admin.product.options')->toHtml()
            ));

        }
        else {
            $this->addTab('set', array(
                'label'     => Mage::helper('catalog')->__('Settings'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_settings')->toHtml(),
                'active'    => true
            ));
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrive product object from object if not from registry
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!($this->getData('product') instanceof Mage_Catalog_Model_Product)) {
            $this->setData('product', Mage::registry('product'));
        }
        return $this->getData('product');
    }

    /**
     * Getting attribute block name for tabs
     *
     * @return string
     */
    public function getAttributeTabBlock()
    {
        return $this->_attributeTabBlock;
    }
}