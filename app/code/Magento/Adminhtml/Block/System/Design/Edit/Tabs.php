<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_System_Design_Edit_Tabs extends Magento_Adminhtml_Block_Widget_Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('design_tabs');
        $this->setDestElementId('design-edit-form');
        $this->setTitle(__('Design Change'));
    }

    protected function _prepareLayout()
    {
        $this->addTab('general', array(
            'label'     => __('General'),
            'content'   => $this->getLayout()
                ->createBlock('Magento_Adminhtml_Block_System_Design_Edit_Tab_General')->toHtml(),
        ));

        return parent::_prepareLayout();
    }
}
