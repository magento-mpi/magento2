<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class Magento_User_Block_Role_Tab_Users
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_User_Block_Role_Tab_Users extends Magento_Backend_Block_Widget_Tabs
{
    /**
     * User model factory
     *
     * @var Magento_User_Model_Resource_User_CollectionFactory
     */
    protected $_userCollectionFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Backend_Model_Auth_Session $authSession
     * @param Magento_User_Model_Resource_User_CollectionFactory $userCollectionFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Backend_Model_Auth_Session $authSession,
        Magento_User_Model_Resource_User_CollectionFactory $userCollectionFactory,
        array $data = array()
    ) {
        // _userCollectionFactory is used in parent::__construct
        $this->_userCollectionFactory = $userCollectionFactory;
        parent::__construct($coreData, $context, $authSession, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $roleId = $this->getRequest()->getParam('rid', false);
        /** @var Magento_User_Model_Resource_User_Collection $users */
        $users = $this->_userCollectionFactory->create()->load();
        $this->setTemplate('role/users.phtml')
            ->assign('users', $users->getItems())
            ->assign('roleId', $roleId);
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'userGrid',
            $this->getLayout()->createBlock('Magento_User_Block_Role_Grid_User', 'roleUsersGrid')
        );
        return parent::_prepareLayout();
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('userGrid');
    }
}
