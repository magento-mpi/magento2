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
 * Admin poll left menu
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Poll_Edit_Tabs extends Magento_Adminhtml_Block_Widget_Tabs
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('poll_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Poll Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => __('Poll Information'),
            'title'     => __('Poll Information'),
            'content'   => $this->getLayout()->createBlock('Magento_Adminhtml_Block_Poll_Edit_Tab_Form')->toHtml(),
        ))
        ;

        $this->addTab('answers_section', array(
                'label'     => __('Poll Answers'),
                'title'     => __('Poll Answers'),
                'content'   => $this->getLayout()->createBlock('Magento_Adminhtml_Block_Poll_Edit_Tab_Answers')
                    ->append($this->getLayout()->createBlock('Magento_Adminhtml_Block_Poll_Edit_Tab_Answers_List'))
                    ->toHtml(),
                'active'    => ( $this->getRequest()->getParam('tab') == 'answers_section' ) ? true : false,
            ));
        return parent::_beforeToHtml();
    }
}
