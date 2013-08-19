<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * description
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Attribute_Set_Toolbar_Add extends Magento_Adminhtml_Block_Template
{

    protected $_template = 'catalog/product/attribute/set/toolbar/add.phtml';

    protected function _prepareLayout()
    {
        $this->addChild('save_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => __('Save Attribute Set'),
            'class' => 'save',
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#set-prop-form'),
                ),
            ),
        ));
        $this->addChild('back_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => __('Back'),
            'onclick'   => 'setLocation(\''.$this->getUrl('*/*/').'\')',
            'class' => 'back'
        ));

        $this->addChild('setForm', 'Magento_Adminhtml_Block_Catalog_Product_Attribute_Set_Main_Formset');
        return parent::_prepareLayout();
    }

    protected function _getHeader()
    {
        return __('Add New Attribute Set');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getFormHtml()
    {
        return $this->getChildHtml('setForm');
    }

    /**
     * Return id of form, used by this block
     *
     * @return string
     */
    public function getFormId()
    {
        return $this->getChildBlock('setForm')->getForm()->getId();
    }
}
