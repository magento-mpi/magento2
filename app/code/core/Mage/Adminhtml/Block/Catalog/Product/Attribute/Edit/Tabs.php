<?php
/**
 * Adminhtml product attribute edit page tabs
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('product_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Attribute Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main', array(
            'label'     => __('General Information'),
            'title'     => __('General Information'),
            'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_edit_tab_main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('system', array(
            'label'     => __('System Propertis'),
            'title'     => __('System Propertis'),
            'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_edit_tab_system')->toHtml(),
        ));

        $model = Mage::registry('entity_attribute');

        $this->addTab('labels', array(
            'label'     => __('Manage Label / Options'),
            'title'     => __('Manage Label / Options'),
            'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_edit_tab_options')->toHtml(),
        ));
        
        /*if ('select' == $model->getFrontendInput()) {
            $this->addTab('options_section', array(
                'label'     => __('Options Control'),
                'title'     => __('Options Control'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_edit_tab_options')->toHtml(),
            ));
        }*/

        return parent::_beforeToHtml();
    }

}
