<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_User_Block_Role_Edit extends Magento_Backend_Block_Widget_Tabs
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('role_info_tabs');
        $this->setDestElementId('role-edit-form');
        $this->setTitle(__('Role Information'));
    }

    protected function _prepareLayout()
    {
        $role = $this->_coreRegistry->registry('current_role');

        $this->addTab(
            'info',
            $this->getLayout()
                ->createBlock('Magento_User_Block_Role_Tab_Info')
                ->setRole($role)
                ->setActive(true)
        );

        if ($role->getId()) {
            $this->addTab('roles', array(
                'label'     => __('Role Users'),
                'title'     => __('Role Users'),
                'content'   => $this->getLayout()
                    ->createBlock('Magento_User_Block_Role_Tab_Users', 'role.users.grid')
                    ->toHtml(),
            ));
        }

        return parent::_prepareLayout();
    }
}
