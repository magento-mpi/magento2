<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Index_Block_Adminhtml_Process_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('process_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Index'));
    }
}
