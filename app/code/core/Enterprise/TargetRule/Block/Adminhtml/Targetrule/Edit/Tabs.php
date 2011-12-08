<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enterprise TargetRule left-navigation block
 *
 */
class Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('targetrule_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Product Rule Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main_section', array(
            'label'     => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Rule Information'),
            'content'   => $this->getLayout()->createBlock(
                'Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Main'
            )->toHtml(),
            'active'    => true
        ));

        $this->addTab('conditions_section', array(
            'label'     => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Products to Match'),
            'content'   => $this->getLayout()->createBlock(
                'Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Conditions'
            )->toHtml(),
        ));

        $this->addTab('targeted_products', array(
            'label'     => Mage::helper('Enterprise_TargetRule_Helper_Data')->__('Products to Display'),
            'content'   => $this->getLayout()->createBlock(
                'Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Actions'
            )->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}
