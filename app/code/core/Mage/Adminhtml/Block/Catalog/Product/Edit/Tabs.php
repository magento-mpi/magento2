<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
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
    protected $_attributeTabBlock = 'Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes';

    public function __construct()
    {
        parent::__construct();
        $this->setId('product_info_tabs');
        $this->setDestElementId('product_edit_form');
        $this->setTitle(Mage::helper('Mage_Catalog_Helper_Data')->__('Product Information'));
    }

    protected function _prepareLayout()
    {
        $product = $this->getProduct();

        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }

        if ($setId) {
            $groupCollection = Mage::getResourceModel('Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection')
                ->setAttributeSetFilter($setId)
                ->setSortOrder()
                ->load();

            foreach ($groupCollection as $group) {
                $attributes = $product->getAttributes($group->getId(), true);
                // do not add groups without attributes

                foreach ($attributes as $key => $attribute) {
                    if( !$attribute->getIsVisible() ) {
                        unset($attributes[$key]);
                    }
                }

                if (count($attributes)==0) {
                    continue;
                }

                $this->addTab('group_'.$group->getId(), array(
                    'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__($group->getAttributeGroupName()),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock($this->getAttributeTabBlock(),
                        'adminhtml.catalog.product.edit.tab.attributes')->setGroup($group)
                            ->setGroupAttributes($attributes)
                            ->toHtml()),
                ));
            }

            if (Mage::helper('Mage_Core_Helper_Data')->isModuleEnabled('Mage_CatalogInventory')) {
                $this->addTab('inventory', array(
                    'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Inventory'),
                    'content'   => $this->_translateHtml($this->getLayout()
                        ->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory')->toHtml()),
                ));
            }

            /**
             * Don't display website tab for single mode
             */
            if (!Mage::app()->isSingleStoreMode()) {
                $this->addTab('websites', array(
                    'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Websites'),
                    'content'   => $this->_translateHtml($this->getLayout()
                        ->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Websites')->toHtml()),
                ));
            }

            $this->addTab('categories', array(
                'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Categories'),
                'url'       => $this->getUrl('*/*/categories', array('_current' => true)),
                'class'     => 'ajax',
            ));

            $this->addTab('related', array(
                'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Related Products'),
                'url'       => $this->getUrl('*/*/related', array('_current' => true)),
                'class'     => 'ajax',
            ));

            $this->addTab('upsell', array(
                'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Up-sells'),
                'url'       => $this->getUrl('*/*/upsell', array('_current' => true)),
                'class'     => 'ajax',
            ));

            $this->addTab('crosssell', array(
                'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Cross-sells'),
                'url'       => $this->getUrl('*/*/crosssell', array('_current' => true)),
                'class'     => 'ajax',
            ));

            $storeId = 0;
            if ($this->getRequest()->getParam('store')) {
                $storeId = Mage::app()->getStore($this->getRequest()->getParam('store'))->getId();
            }

            $alertPriceAllow = Mage::getStoreConfig('catalog/productalert/allow_price');
            $alertStockAllow = Mage::getStoreConfig('catalog/productalert/allow_stock');

            if (($alertPriceAllow || $alertStockAllow) && !$product->isGrouped()) {
                $this->addTab('productalert', array(
                    'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Product Alerts'),
                    'content'   => $this->_translateHtml($this->getLayout()
                        ->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Alerts', 'admin.alerts.products')
                        ->toHtml()
                    )
                ));
            }

            if( $this->getRequest()->getParam('id', false) ) {
                if (Mage::helper('Mage_Catalog_Helper_Data')->isModuleEnabled('Mage_Review')) {
                    if (Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('admin/catalog/reviews_ratings')){
                        $this->addTab('reviews', array(
                            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Product Reviews'),
                            'url'   => $this->getUrl('*/*/reviews', array('_current' => true)),
                            'class' => 'ajax',
                        ));
                    }
                }
                if (Mage::helper('Mage_Catalog_Helper_Data')->isModuleEnabled('Mage_Tag')) {
                    if (Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('admin/catalog/tag')){
                        $this->addTab('tags', array(
                         'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Product Tags'),
                         'url'   => $this->getUrl('*/*/tagGrid', array('_current' => true)),
                         'class' => 'ajax',
                        ));

                        $this->addTab('customers_tags', array(
                            'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Customers Tagged Product'),
                            'url'   => $this->getUrl('*/*/tagCustomerGrid', array('_current' => true)),
                            'class' => 'ajax',
                        ));
                    }
                }

            }

            /**
             * Do not change this tab id
             * @see Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs_Configurable
             * @see Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tabs
             */
            if (!$product->isGrouped()) {
                $this->addTab('customer_options', array(
                    'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Custom Options'),
                    'url'   => $this->getUrl('*/*/options', array('_current' => true)),
                    'class' => 'ajax',
                ));
            }

        }
        else {
            $this->addTab('set', array(
                'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Settings'),
                'content'   => $this->_translateHtml($this->getLayout()
                    ->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Settings')->toHtml()),
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
        if (is_null(Mage::helper('Mage_Adminhtml_Helper_Catalog')->getAttributeTabBlock())) {
            return $this->_attributeTabBlock;
        }
        return Mage::helper('Mage_Adminhtml_Helper_Catalog')->getAttributeTabBlock();
    }

    public function setAttributeTabBlock($attributeTabBlock)
    {
        $this->_attributeTabBlock = $attributeTabBlock;
        return $this;
    }

    /**
     * Translate html content
     *
     * @param string $html
     * @return string
     */
    protected function _translateHtml($html)
    {
        Mage::getSingleton('Mage_Core_Model_Translate_Inline')->processResponseBody($html);
        return $html;
    }
}
