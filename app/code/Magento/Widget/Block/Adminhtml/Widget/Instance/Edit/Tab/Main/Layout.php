<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget Instance page groups (predefined layouts group) to display on
 *
 * @method Magento_Widget_Model_Widget_Instance getWidgetInstance()
 */
class Magento_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main_Layout
    extends Magento_Adminhtml_Block_Template implements Magento_Data_Form_Element_Renderer_Interface
{
    /**
     * @var Magento_Data_Form_Element_Abstract
     */
    protected $_element = null;

    protected $_template = 'instance/edit/layout.phtml';

    /**
     * Render given element (return html of element)
     *
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * Setter
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return
     */
    public function setElement(Magento_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * Getter
     *
     * @return Magento_Data_Form_Element_Abstract
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
     * Generate url to get template chooser by ajax query
     *
     * @return string
     */
    public function getTemplateChooserUrl()
    {
        return $this->getUrl('*/*/template', array('_current' => true));
    }

    /**
     * Create and return html of select box Display On
     *
     * @return string
     */
    public function getDisplayOnSelectHtml()
    {
        $selectBlock = $this->getLayout()->createBlock('Magento_Core_Block_Html_Select')
            ->setName('widget_instance[{{id}}][page_group]')
            ->setId('widget_instance[{{id}}][page_group]')
            ->setClass('required-entry page_group_select select')
            ->setExtraParams("onchange=\"WidgetInstance.displayPageGroup(this.value+\'_{{id}}\')\"")
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
            'label' => $this->helper('Magento_Core_Helper_Data')->jsQuoteEscape(__('-- Please Select --'))
        );
        $options[] = array(
            'label' => __('Categories'),
            'value' => array(
                array(
                    'value' => 'anchor_categories',
                    'label' => $this->helper('Magento_Core_Helper_Data')->jsQuoteEscape(__('Anchor Categories'))
                ),
                array(
                    'value' => 'notanchor_categories',
                    'label' => $this->helper('Magento_Core_Helper_Data')->jsQuoteEscape(__('Non-Anchor Categories'))
                )
            )
        );
        foreach (Magento_Catalog_Model_Product_Type::getTypes() as $typeId => $type) {
            $productsOptions[] = array(
               'value' => $typeId.'_products',
               'label' => $this->helper('Magento_Core_Helper_Data')->jsQuoteEscape($type['label'])
            );
        }
        array_unshift($productsOptions, array(
            'value' => 'all_products',
            'label' => $this->helper('Magento_Core_Helper_Data')->jsQuoteEscape(__('All Product Types'))
        ));
        $options[] = array(
            'label' => $this->helper('Magento_Core_Helper_Data')->jsQuoteEscape(__('Products')),
            'value' => $productsOptions
        );
        $options[] = array(
            'label' => $this->helper('Magento_Core_Helper_Data')->jsQuoteEscape(__('Generic Pages')),
            'value' => array(
                array(
                    'value' => 'all_pages',
                    'label' => $this->helper('Magento_Core_Helper_Data')->jsQuoteEscape(__('All Pages'))
                ),
                array(
                    'value' => 'pages',
                    'label' => $this->helper('Magento_Core_Helper_Data')->jsQuoteEscape(__('Specified Page'))
                )
            )
        );
        return $options;
    }

    /**
     * Generate array of parameters for every container type to create html template
     *
     * @return array
     */
    public function getDisplayOnContainers()
    {
        $container = array();
        $container['anchor'] = array(
            'label' => 'Categories',
            'code' => 'categories',
            'name' => 'anchor_categories',
            'layout_handle' => Magento_Widget_Model_Widget_Instance::ANCHOR_CATEGORY_LAYOUT_HANDLE,
            'is_anchor_only' => 1,
            'product_type_id' => ''
        );
        $container['notanchor'] = array(
            'label' => 'Categories',
            'code' => 'categories',
            'name' => 'notanchor_categories',
            'layout_handle' => Magento_Widget_Model_Widget_Instance::NOTANCHOR_CATEGORY_LAYOUT_HANDLE,
            'is_anchor_only' => 0,
            'product_type_id' => ''
        );
        $container['all_products'] = array(
            'label' => 'Products',
            'code' => 'products',
            'name' => 'all_products',
            'layout_handle' => Magento_Widget_Model_Widget_Instance::PRODUCT_LAYOUT_HANDLE,
            'is_anchor_only' => '',
            'product_type_id' => ''
        );
        foreach (Magento_Catalog_Model_Product_Type::getTypes() as $typeId => $type) {
            $container[$typeId] = array(
                'label' => 'Products',
                'code' => 'products',
                'name' => $typeId . '_products',
                'layout_handle'
                    => str_replace('{{TYPE}}', $typeId, Magento_Widget_Model_Widget_Instance::PRODUCT_TYPE_LAYOUT_HANDLE),
                'is_anchor_only' => '',
                'product_type_id' => $typeId
            );
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
        $chooserBlock = $this->getLayout()
            ->createBlock('Magento_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Layout')
            ->setName('widget_instance[{{id}}][pages][layout_handle]')
            ->setId('layout_handle')
            ->setClass('required-entry select')
            ->setExtraParams("onchange=\"WidgetInstance.loadSelectBoxByType(\'block_reference\', "
                . "this.up(\'div.pages\'), this.value)\"")
            ->setArea($this->getWidgetInstance()->getArea())
            ->setTheme($this->getWidgetInstance()->getThemeId())
        ;
        return $chooserBlock->toHtml();
    }

    /**
     * Retrieve add layout button html
     *
     * @return string
     */
    public function getAddLayoutButtonHtml()
    {
        $button = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'     => __('Add Layout Update'),
                'onclick'   => 'WidgetInstance.addPageGroup({})',
                'class'     => 'action-add'
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
        $button = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'     => $this->helper('Magento_Core_Helper_Data')->jsQuoteEscape(__('Remove Layout Update')),
                'onclick'   => 'WidgetInstance.removePageGroup(this)',
                'class'     => 'action-delete'
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
                    'group' => $pageGroup['page_group'],
                    'block' => $pageGroup['block_reference'],
                    'for_value'   => $pageGroup['page_for'],
                    'layout_handle' => $pageGroup['layout_handle'],
                    $pageGroup['page_group'].'_entities' => $pageGroup['entities'],
                    'template' => $pageGroup['page_template']
                );
            }
        }
        return $pageGroups;
    }
}
