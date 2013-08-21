<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enterprise TargetRule left-navigation block
 *
 */
class Magento_TargetRule_Block_Adminhtml_Targetrule_Edit_Tabs extends Magento_Adminhtml_Block_Widget_Tabs
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('targetrule_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Product Rule Information'));
    }
}
