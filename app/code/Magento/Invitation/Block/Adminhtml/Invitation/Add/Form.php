<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invintation create form
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
class Magento_Invitation_Block_Adminhtml_Invitation_Add_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Magento Store
     *
     * @var Magento_Core_Model_System_Store
     */
    protected $_store;

    /**
     * Customer Group Factory
     *
     * @var Magento_Customer_Model_GroupFactory
     */
    protected $_groupFactory;

    /**
     * Admin Session
     *
     * @var Magento_Adminhtml_Model_Session
     */
    protected $_session;

    /**
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_System_Store $store
     * @param Magento_Customer_Model_GroupFactory $groupFactory
     * @param Magento_Adminhtml_Model_Session $session
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_System_Store $store,
        Magento_Customer_Model_GroupFactory $groupFactory,
        Magento_Adminhtml_Model_Session $session,
        array $data = array()
    ) {
        parent::__construct($registry,$formFactory,$coreData,$context,$data);
        $this->_storeManager = $storeManager;
        $this->_store = $store;
        $this->_groupFactory = $groupFactory;
        $this->_session = $session;
    }

    /**
     * Return invitation form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true));
    }

    /**
     * Prepare invitation form
     *
     * @return Magento_Invitation_Block_Adminhtml_Invitation_Add_Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'edit_form',
                'action' => $this->getActionUrl(),
                'method' => 'post',
            ))
        );

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('Invitations Information'),
            'class' => 'fieldset-wide'
        ));

        $fieldset->addField('email', 'textarea', array(
            'label' => __('Enter Each Email on New Line'),
            'required' => true,
            'class' => 'validate-emails',
            'name' => 'email'
        ));

        $fieldset->addField('message', 'textarea', array(
            'label' => __('Message'),
            'name' => 'message'
        ));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'label' => __('Send From'),
                'required' => true,
                'name' => 'store_id',
                'values' => $this->_store->getStoreValuesForForm(),
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
        }

        $groups = $this->_groupFactory->create()->getCollection()
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $fieldset->addField('group_id', 'select', array(
            'label' => __('Invitee Group'),
            'required' => true,
            'name' => 'group_id',
            'values' => $groups
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        $form->setValues($this->_session->getInvitationFormData());

        return parent::_prepareForm();
    }

}
