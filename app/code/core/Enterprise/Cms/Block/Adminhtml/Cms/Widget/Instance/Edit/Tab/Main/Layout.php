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
 * @package     Enterprise_Cms
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Cms Widget Instance page groups (predefined layouts group) to display on
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Block_Adminhtml_Cms_Widget_Instance_Edit_Tab_Main_Layout
    extends Mage_Adminhtml_Block_Template implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * @var Varien_Data_Form_Element_Abstract
     */
    protected $_element = null;

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('enterprise/cms/widget/instance/edit/layout.phtml');
    }

    /**
     * Render given element (return html of element)
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * Setter
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Widget_Instance_Edit_Tab_Main_Layout
     */
    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * Getter
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Generate url to get categories chooser by ajax query
     *
     * @return string
     */
    public function getCategoriesChooserUrl()
    {
        return $this->getUrl('*/*/categories', array('_current' => true));
    }

    /**
     * Generate url to get products chooser by ajax query
     *
     * @return string
     */
    public function getProductsChooserUrl()
    {
        return $this->getUrl('*/*/products', array('_current' => true));
    }

    /**
     * Generate url to get reference block chooser by ajax query
     *
     * @return string
     */
    public function getBlockChooserUrl()
    {
        return $this->getUrl('*/*/blocks', array('_current' => true));
    }

    /**
     * Create and return html of select box Display On
     *
     * @return string
     */
    public function getDisplayOnSelectHtml()
    {
        $selectBlock = $this->getLayout()->createBlock('core/html_select')
            ->setName('widget_instance[{{id}}][page_group]')
            ->setId('widget_instance[{{id}}][page_group]')
            ->setClass('page_group_select')
            ->setExtraParams('onchange="WidgetInstance.displayPageGroup(this.value+\'_{{id}}\')"')
            ->setOptions($this->_getDisplayOnOptions());
        return $selectBlock->toHtml();
    }

    /**
     * Retrieve Display On options array.
     * - Categories (anchor and not anchor)
     * - Products (product types depend on configuration)
     * - Generic (predefined) pages (all pages and single layout update)
     *
     * @return array
     */
    protected function _getDisplayOnOptions()
    {
        $options = array();
        $options[] = array(
            'value' => '',
            'label' => Mage::helper('enterprise_cms')->__('-- Please Select --')
        );
        $options[] = array(
            'label' => Mage::helper('enterprise_cms')->__('Categories'),
            'value' => array(
                array(
                    'value' => 'anchor_categories',
                    'label' => Mage::helper('enterprise_cms')->__('Anchor Categories')
                ),
                array(
                    'value' => 'notanchor_categories',
                    'label' => Mage::helper('enterprise_cms')->__('Not Anchor Categories')
                )
            )
        );
        foreach (Mage_Catalog_Model_Product_Type::getTypes() as $typeId => $type) {
            $productsOptions[] = array(
               'value' => $typeId.'_products',
               'label' => $type['label']
            );
        }
        array_unshift($productsOptions, array(
            'value' => 'all_products',
            'label' => Mage::helper('enterprise_cms')->__('All Products')
        ));
        $options[] = array(
            'label' => Mage::helper('enterprise_cms')->__('Products'),
            'value' => $productsOptions
        );
        $options[] = array(
            'label' => Mage::helper('enterprise_cms')->__('Generic Pages'),
            'value' => array(
                array(
                    'value' => 'all_pages',
                    'label' => Mage::helper('enterprise_cms')->__('All Pages')
                ),
                array(
                    'value' => 'pages',
                    'label' => Mage::helper('enterprise_cms')->__('Specified Page')
                )
            )
        );
        return $options;
    }

    /**
     * Generate array of parameters for given container type to create html template
     *
     * @param string $type
     * @return array
     */
    public function getDisplayOnContainers($type)
    {
        $container = array();
        switch ($type) {
            case 'categories':
                $container['anchor'] = array(
                    'name' => 'anchor_categories',
                    'layout_handle' => 'default,catalog_category_layered',
                    'is_anchor_only' => 1
                );
                $container['notanchor'] = array(
                    'name' => 'notanchor_categories',
                    'layout_handle' => 'default,catalog_category_default',
                    'is_anchor_only' => 0
                );
                break;
            case 'products':
                $container['all_products'] = array(
                    'name' => 'all_products',
                    'layout_handle' => 'default,catalog_product_view',
                    'product_type_id' => ''
                );
                foreach (Mage_Catalog_Model_Product_Type::getTypes() as $typeId => $type) {
                    $container[$typeId] = array(
                        'name' => $typeId . '_products',
                        'layout_handle' => 'default,catalog_product_view,PRODUCT_TYPE_'.$typeId,
                        'product_type_id' => $typeId
                    );
                }
                break;
        }
        return $container;
    }

    /**
     * Retrieve layout select chooser html
     *
     * @return string
     */
    public function getLayoutsChooser()
    {
        $layouts = $this->getLayout()
            ->createBlock('enterprise_cms/adminhtml_cms_widget_instance_edit_chooser_layout')
            ->setSelectName('widget_instance[{{id}}][pages][layout_handle]')
            ->setArea($this->getWidgetInstance()->getArea())
            ->setPackage($this->getWidgetInstance()->getPackage())
            ->setTheme($this->getWidgetInstance()->getTheme());
        return $layouts->toHtml();
    }

    /**
     * Retrieve add layout button html
     *
     * @return string
     */
    public function getAddLayoutButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('enterprise_cms')->__('Add Layout Update'),
                'onclick'   => 'WidgetInstance.addPageGroup({})',
                'class'     => 'add'
            ));
        return $button->toHtml();
    }

    /**
     * Retrieve remove layout button html
     *
     * @return string
     */
    public function getRemoveLayoutButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('enterprise_cms')->__('Remove Layout Update'),
                'onclick'   => 'WidgetInstance.removePageGroup(this)',
                'class'     => 'delete'
            ));
        return $button->toHtml();
    }

    /**
     * Prepare and retrieve page groups data of widget instance
     *
     * @return array
     */
    public function getPageGroups()
    {
        $widgetInstance = $this->getWidgetInstance();
        $pageGroups = array();
        if ($widgetInstance->getPageGroups()) {
            foreach ($widgetInstance->getPageGroups() as $pageGroup) {
                $pageGroups[] = array(
                    'page_id' => $pageGroup['page_id'],
                    'group' => $pageGroup['group'],
                    'block' => $pageGroup['block_reference'],
                    'for_value'   => $pageGroup['for'],
                    'layout_handle' => $pageGroup['layout_handle'],
                    $pageGroup['group'].'_entities' => $pageGroup['entities'],
                    'position' => $pageGroup['position']
                );
            }
        }
        return $pageGroups;
    }
}
