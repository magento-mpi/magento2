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
    const BASIC_TAB_GROUP_CODE = 'basic';
    const ADVANCED_TAB_GROUP_CODE = 'advanced';

    /** @var string */
    protected $_attributeTabBlock = 'Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes';

    /** @var string */
    protected $_template = 'Mage_Catalog::product/edit/tabs.phtml';

    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_info_tabs');
        $this->setDestElementId('product-edit-form-tabs');
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

            $tabAttributesBlock = $this->getLayout()->createBlock(
                $this->getAttributeTabBlock(), $this->getNameInLayout() . '_attributes_tab'
            );
            foreach ($groupCollection as $group) {
                /** @var $group Mage_Eav_Model_Entity_Attribute_Group*/
                $attributes = $product->getAttributes($group->getId(), true);

                foreach ($attributes as $key => $attribute) {
                    if (!$attribute->getIsVisible()) {
                        unset($attributes[$key]);
                    }
                }

                if ($attributes) {
                    $this->addTab($group->getAttributeGroupCode(), array(
                        'label'   => Mage::helper('Mage_Catalog_Helper_Data')->__($group->getAttributeGroupName()),
                        'content' => $this->_translateHtml(
                            $tabAttributesBlock->setGroup($group)
                                ->setGroupAttributes($attributes)
                                ->toHtml()
                        ),
                        'group_code' => !in_array($group->getAttributeGroupCode(), array(
                            'advanced-pricing', 'design', 'autosettings'
                        )) ? self::BASIC_TAB_GROUP_CODE : self::ADVANCED_TAB_GROUP_CODE
                    ));
                }
            }

            if (Mage::helper('Mage_Core_Helper_Data')->isModuleEnabled('Mage_CatalogInventory')) {
                $this->addTab('inventory', array(
                    'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Advanced Inventory'),
                    'content'   => $this->_translateHtml($this->getLayout()
                        ->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory')->toHtml()),
                    'group_code' => self::ADVANCED_TAB_GROUP_CODE,
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
                    'group_code' => self::ADVANCED_TAB_GROUP_CODE,
                ));
            }

            $this->addTab('related', array(
                'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Related Products'),
                'url'       => $this->getUrl('*/*/related', array('_current' => true)),
                'class'     => 'ajax',
                'group_code' => self::ADVANCED_TAB_GROUP_CODE,
            ));

            $this->addTab('upsell', array(
                'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Up-sells'),
                'url'       => $this->getUrl('*/*/upsell', array('_current' => true)),
                'class'     => 'ajax',
                'group_code' => self::ADVANCED_TAB_GROUP_CODE,
            ));

            $this->addTab('crosssell', array(
                'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Cross-sells'),
                'url'       => $this->getUrl('*/*/crosssell', array('_current' => true)),
                'class'     => 'ajax',
                'group_code' => self::ADVANCED_TAB_GROUP_CODE,
            ));

            $alertPriceAllow = Mage::getStoreConfig('catalog/productalert/allow_price');
            $alertStockAllow = Mage::getStoreConfig('catalog/productalert/allow_stock');
            if (($alertPriceAllow || $alertStockAllow) && !$product->isGrouped()) {
                $this->addTab('productalert', array(
                    'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Product Alerts'),
                    'content'   => $this->_translateHtml($this->getLayout()
                        ->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Alerts', 'admin.alerts.products')
                        ->toHtml()
                    ),
                    'group_code' => self::ADVANCED_TAB_GROUP_CODE,
                ));
            }

            if ($this->getRequest()->getParam('id', false)) {
                if (Mage::helper('Mage_Catalog_Helper_Data')->isModuleEnabled('Mage_Review')) {
                    if (Mage::getSingleton('Mage_Core_Model_Authorization')
                        ->isAllowed('Mage_Review::reviews_ratings')
                    ) {
                        $this->addTab('reviews', array(
                            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Product Reviews'),
                            'url'   => $this->getUrl('*/*/reviews', array('_current' => true)),
                            'class' => 'ajax',
                            'group_code' => self::ADVANCED_TAB_GROUP_CODE,
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
                    'group_code' => self::ADVANCED_TAB_GROUP_CODE,
                ));
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Check whether active tab belong to advanced group
     *
     * @return bool
     */
    public function isAdvancedTabGroupActive()
    {
        return $this->_tabs[$this->_activeTab]->getGroupCode() == self::ADVANCED_TAB_GROUP_CODE;
    }

    /**
     * Retrieve product object from object if not from registry
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
        Mage::getObjectManager()->get('Mage_Core_Model_Translate')->processResponseBody($html);
        return $html;
    }
}
