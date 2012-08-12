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
 * Adminhtml product attribute edit page tabs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Mage_Catalog_Helper_Data')->__('Attribute Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main', array(
            'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Properties'),
            'title'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Properties'),
            'content'   => $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Main')->toHtml(),
            'active'    => true
        ));

        $model = Mage::registry('entity_attribute');

        $this->addTab('labels', array(
            'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Manage Label / Options'),
            'title'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Manage Label / Options'),
            'content'   => $this->getLayout()
                ->createBlock('Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Options')->toHtml(),
        ));
        
        /*if ('select' == $model->getFrontendInput()) {
            $this->addTab('options_section', array(
                'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Options Control'),
                'title'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Options Control'),
                'content'   => $this->getLayout()
                    ->createBlock('Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Options')->toHtml(),
            ));
        }*/

        return parent::_beforeToHtml();
    }

}
