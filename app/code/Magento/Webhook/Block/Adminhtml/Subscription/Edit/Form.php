<?php
/**
 * Form to edit webhook subscription
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Magento_Webhook_Block_Adminhtml_Subscription_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /** Key used to store subscription data into the registry */
    const REGISTRY_KEY_CURRENT_SUBSCRIPTION = 'current_subscription';

    /** Keys used to retrieve values from subscription data array */
    const DATA_SUBSCRIPTION_ID = 'subscription_id';
    const DATA_ALIAS = 'alias';

    /** @var  Magento_Webhook_Model_Source_Format $_format */
    protected $_format;

    /** @var  Magento_Webhook_Model_Source_Authentication $_authentication */
    protected $_authentication;

    /** @var  Magento_Webhook_Model_Source_Hook  $_hook */
    protected $_hook;

    /**
     * @param Magento_Webhook_Model_Source_Format $format
     * @param Magento_Webhook_Model_Source_Authentication $authentication
     * @param Magento_Webhook_Model_Source_Hook $hook
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Webhook_Model_Source_Format $format,
        Magento_Webhook_Model_Source_Authentication $authentication,
        Magento_Webhook_Model_Source_Hook $hook,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
        $this->_formFactory = $formFactory;
        $this->_registry = $registry;
        $this->_format = $format;
        $this->_authentication = $authentication;
        $this->_hook = $hook;
    }

    /**
     * Prepares subscription editor form
     *
     * @return Magento_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $subscriptionData = $this->_registry->registry(self::REGISTRY_KEY_CURRENT_SUBSCRIPTION);

        $subscriptionId = isset($subscriptionData[self::DATA_SUBSCRIPTION_ID])
            ? $subscriptionData[self::DATA_SUBSCRIPTION_ID]
            : 0;
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                 'id'     => 'edit_form',
                 'action' => $this->getUrl('*/*/save', array('id' => $subscriptionId)),
                 'method' => 'post',
            ))
        );

        // We don't want to allow subscriptions defined in config to be edited by the user.
        $disabled = isset($subscriptionData[self::DATA_ALIAS]) && !empty($subscriptionData[self::DATA_ALIAS]);

        $fieldset = $form->addFieldset('subscription_fieldset', array('legend' => __('Subscription')));

        $fieldset->addField(
            'name', 'text',
            array(
                'label'     => __('Name'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'name',
                'disabled'  => $disabled,
            )
        );

        $fieldset->addField(
            'endpoint_url', 'text',
            array(
                'label'     => __('Endpoint URL'),
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
                'label'     => __('Format'),
                'title'     => __('Format'),
                'values'    => $this->_format->getFormatsForForm(),
                'disabled'  => $disabled,
            )
        );

        $fieldset->addField(
            'authentication_type', 'select',
            array(
                'name'      => 'authentication_type',
                'label'     => __('Authentication Types'),
                'title'     => __('Authentication Types'),
                'values'    => $this->_authentication->getAuthenticationsForForm(),
                'disabled'  => $disabled,
            )
        );

        $fieldset->addField(
            'topics', 'multiselect',
            array(
                'name'      => 'topics[]',
                'label'     => __('Topics'),
                'title'     => __('Topics'),
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
