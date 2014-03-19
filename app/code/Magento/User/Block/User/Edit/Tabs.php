<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Block\User\Edit;

/**
 * User page left menu
 *
 * @category   Magento
 * @package    Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('User Information'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'main_section',
            array(
                'label' => __('User Info'),
                'title' => __('User Info'),
                'content' => $this->getLayout()->createBlock('Magento\User\Block\User\Edit\Tab\Main')->toHtml(),
                'active' => true
            )
        );

        $this->addTab(
            'roles_section',
            array(
                'label' => __('User Role'),
                'title' => __('User Role'),
                'content' => $this->getLayout()->createBlock(
                    'Magento\User\Block\User\Edit\Tab\Roles',
                    'user.roles.grid'
                )->toHtml()
            )
        );
        return parent::_beforeToHtml();
    }
}
