<?php
/**
 * Form to edit webhook subscription
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Block_Adminhtml_Subscription_Edit_Form extends Mage_Backend_Block_Widget_Form
{
    /** Key used to store subscription data into the registry */
    const REGISTRY_KEY_CURRENT_SUBSCRIPTION = 'current_subscription';

    /** Keys used to retrieve values from subscription data array */
    const DATA_SUBSCRIPTION_ID = 'subscription_id';
    const DATA_ALIAS = 'alias';

    /** @var Magento_Data_Form_Factory $_formFactory */
    private $_formFactory;

    /** @var  Mage_Core_Model_Registry $_registry */
    private $_registry;

    /** @var  Mage_Webhook_Model_Source_Format $_format */
    private $_format;

    /** @var  Mage_Webhook_Model_Source_Authentication $_authentication */
    private $_authentication;

    /** @var  Mage_Webhook_Model_Source_Hook  $_hook */
    private $_hook;

    /**
     * @param Magento_Data_Form_Factory $formFactory
     * @param Mage_Core_Model_Registry $registry
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Webhook_Model_Source_Format $format
     * @param Mage_Webhook_Model_Source_Authentication $authentication
     * @param Mage_Webhook_Model_Source_Hook $hook
     * @param array $data
     */
    public function __construct(
        Magento_Data_Form_Factory $formFactory,
        Mage_Core_Model_Registry $registry,
        Mage_Backend_Block_Template_Context $context,
        Mage_Webhook_Model_Source_Format $format,
        Mage_Webhook_Model_Source_Authentication $authentication,
        Mage_Webhook_Model_Source_Hook $hook,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_formFactory = $formFactory;
        $this->_registry = $registry;
        $this->_format = $format;
        $this->_authentication = $authentication;
        $this->_hook = $hook;
    }


    /**
     * Prepares subscription editor form
     *
     * @return Mage_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $subscriptionData = $this->_registry->registry(self::REGISTRY_KEY_CURRENT_SUBSCRIPTION);

        $subscriptionId = isset($subscriptionData[self::DATA_SUBSCRIPTION_ID])
            ? $subscriptionData[self::DATA_SUBSCRIPTION_ID]
            : 0;
        $form = $this->_formFactory->create(
            array(
                 'id'     => 'edit_form',
                 'action' => $this->getUrl(
                     '*/*/save',
                     array('id' => $subscriptionId)
                 ),
                 'method' => 'post'
            )
        );

        // We don't want to allow subscriptions defined in config to be edited by the user.
        $disabled = isset($subscriptionData[self::DATA_ALIAS]) && !empty($subscriptionData[self::DATA_ALIAS]);

        $fieldset = $form->addFieldset('subscription_fieldset', array('legend' => $this->__('Subscription')));

        $fieldset->addField(
            'name', 'text',
            array(
                'label'     => $this->__('Name'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'name',
                'disabled'  => $disabled,
            )
        );

        $fieldset->addField(
            'endpoint_url', 'text',
            array(
                'label'     => $this->__('Endpoint URL'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'endpoint_url',
                'disabled'  => $disabled,
            )
        );

        $fieldset->addField(
            'format', 'select',
            array(
                'name'      => 'format',
                'label'     => $this->__('Format'),
                'title'     => $this->__('Format'),
                'values'    => $this->_format->getFormatsForForm(),
                'disabled'  => $disabled,
            )
        );

        $fieldset->addField(
            'authentication_type', 'select',
            array(
                'name'      => 'authentication_type',
                'label'     => $this->__('Authentication Types'),
                'title'     => $this->__('Authentication Types'),
                'values'    => $this->_authentication->getAuthenticationsForForm(),
                'disabled'  => $disabled,
            )
        );

        $fieldset->addField(
            'topics', 'multiselect',
            array(
                'name'      => 'topics[]',
                'label'     => $this->__('Topics'),
                'title'     => $this->__('Topics'),
                'required'  => true,
                'values'    => $this->_hook->getTopicsForForm(),
                'disabled'  => $disabled,
            )
        );

        $form->setUseContainer(true);
        $form->setValues($subscriptionData);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
