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
 * Admin rating left menu
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Rating_Edit_Tabs extends Magento_Adminhtml_Block_Widget_Tabs
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('rating_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Rating Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => __('Rating Information'),
            'title'     => __('Rating Information'),
            'content'   => $this->getLayout()->createBlock('Magento_Adminhtml_Block_Rating_Edit_Tab_Form')->toHtml(),
        ))
        ;
/*
        $this->addTab('answers_section', array(
                'label'     => __('Rating Options'),
                'title'     => __('Rating Options'),
                'content'   => $this->getLayout()->createBlock('Magento_Adminhtml_Block_Rating_Edit_Tab_Options')
                    ->append($this->getLayout()->createBlock('Magento_Adminhtml_Block_Rating_Edit_Tab_Options'))
                    ->toHtml(),
           ));*/
        return parent::_beforeToHtml();
    }
}
