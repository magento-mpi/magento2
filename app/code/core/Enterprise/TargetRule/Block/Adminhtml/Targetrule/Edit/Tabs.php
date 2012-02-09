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
}
