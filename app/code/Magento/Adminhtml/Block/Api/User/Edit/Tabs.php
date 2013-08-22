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
 * Admin page left menu
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Api_User_Edit_Tabs extends Magento_Adminhtml_Block_Widget_Tabs
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('User Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main_section', array(
            'label'     => __('User Info'),
            'title'     => __('User Info'),
            'content'   => $this->getLayout()->createBlock('Magento_Adminhtml_Block_Api_User_Edit_Tab_Main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('roles_section', array(
            'label'     => __('User Role'),
            'title'     => __('User Role'),
            'content'   => $this->getLayout()->createBlock(
                'Magento_Adminhtml_Block_Api_User_Edit_Tab_Roles',
                'user.roles.grid'
            )->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
