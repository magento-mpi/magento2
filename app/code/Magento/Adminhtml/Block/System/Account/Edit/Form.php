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
 * Adminhtml edit admin user account form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_System_Account_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * @var Magento_User_Model_UserFactory
     */
    protected $_userFactory;

    /**
     * @param Magento_User_Model_UserFactory $userFactory
     * @param Magento_Backend_Model_Auth_Session $authSession
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_User_Model_UserFactory $userFactory,
        Magento_Backend_Model_Auth_Session $authSession,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_userFactory = $userFactory;
        $this->_authSession = $authSession;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    protected function _prepareForm()
    {
        $userId = $this->_authSession->getUser()->getId();
        $user = $this->_userFactory->create()->load($userId);
        $user->unsetData('password');

        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Account Information')));

        $fieldset->addField('username', 'text', array(
            'name'  => 'username',
            'label' => __('User Name'),
            'title' => __('User Name'),
            'required' => true,
        ));

        $fieldset->addField('firstname', 'text', array(
            'name'  => 'firstname',
            'label' => __('First Name'),
            'title' => __('First Name'),
            'required' => true,
        ));

        $fieldset->addField('lastname', 'text', array(
            'name'  => 'lastname',
            'label' => __('Last Name'),
            'title' => __('Last Name'),
            'required' => true,
        ));

        $fieldset->addField('user_id', 'hidden', array(
            'name'  => 'user_id',
        ));

        $fieldset->addField('email', 'text', array(
            'name'  => 'email',
            'label' => __('Email'),
            'title' => __('User Email'),
            'required' => true,
        ));

        $fieldset->addField('password', 'password', array(
            'name'  => 'password',
            'label' => __('New Password'),
            'title' => __('New Password'),
            'class' => 'input-text validate-admin-password',
        ));

        $fieldset->addField('confirmation', 'password', array(
            'name'  => 'password_confirmation',
            'label' => __('Password Confirmation'),
            'class' => 'input-text validate-cpassword',
        ));

        $fieldset->addField('interface_locale', 'select', array(
            'name'   => 'interface_locale',
            'label'  => __('Interface Locale'),
            'title'  => __('Interface Locale'),
            'values' => $this->_locale->getTranslatedOptionLocales(),
            'class'  => 'select',
        ));

        $form->setValues($user->getData());
        $form->setAction($this->getUrl('*/system_account/save'));
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
