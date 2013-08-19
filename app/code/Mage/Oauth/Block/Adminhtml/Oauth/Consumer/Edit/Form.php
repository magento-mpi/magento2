<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth consumer edit form block
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Block_Adminhtml_Oauth_Consumer_Edit_Form extends Mage_Backend_Block_Widget_Form
{
    /** Key used to store consumer data into the registry */
    const REGISTRY_KEY_CURRENT_CONSUMER = 'current_consumer';

    /** Keys used to retrieve values from subscription data array */
    const DATA_ENTITY_ID = 'entity_id';

    /** @var Varien_Data_Form_Factory $_formFactory */
    private $_formFactory;

    /** @var Mage_Core_Model_Registry $_registry */
    private $_registry;

    /**
     * @param Varien_Data_Form_Factory $formFactory
     * @param Mage_Core_Model_Registry $registry
     * @param Mage_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Varien_Data_Form_Factory $formFactory,
        Mage_Core_Model_Registry $registry,
        Mage_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_formFactory = $formFactory;
        $this->_registry = $registry;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Oauth_Block_Adminhtml_Oauth_Consumer_Edit_Form
     */
    protected function _prepareForm()
    {
        $consumerData = $this->_registry->registry(self::REGISTRY_KEY_CURRENT_CONSUMER);

        $form = $this->_formFactory->create(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save',
                    $consumerData[self::DATA_ENTITY_ID]
                        ? array('id' => $consumerData[self::DATA_ENTITY_ID]) : array()),
                'method' => 'post')
            );

        $fieldset = $form->addFieldset('consumer_fieldset', array(
            'legend' => $this->__('Add-On Information'), 'class' => 'fieldset-wide'
        ));

        if ($consumerData[self::DATA_ENTITY_ID]) {
            $fieldset->addField(
                'id', 'hidden', array('name' => 'id', 'value' => $consumerData[self::DATA_ENTITY_ID]));
        }

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => $this->__('Name'),
            'title'     => $this->__('Name'),
            'required'  => true
        ));

        $fieldset->addField('key', 'text', array(
            'name'      => 'key',
            'label'     => $this->__('Key'),
            'title'     => $this->__('Key'),
            'disabled'  => true,
            'required'  => true
        ));

        $fieldset->addField('secret', 'text', array(
            'name'      => 'secret',
            'label'     => $this->__('Secret'),
            'title'     => $this->__('Secret'),
            'disabled'  => true,
            'required'  => true
        ));

        $fieldset->addField('callback_url', 'text', array(
            'name'      => 'callback_url',
            'label'     => $this->__('Callback URL'),
            'title'     => $this->__('Callback URL'),
            'required'  => false,
            'class'     => 'validate-url',
        ));

        $fieldset->addField('rejected_callback_url', 'text', array(
            'name'      => 'rejected_callback_url',
            'label'     => $this->__('Rejected Callback URL'),
            'title'     => $this->__('Rejected Callback URL'),
            'required'  => false,
            'class'     => 'validate-url',
        ));

        $fieldset->addField('http_post_url', 'text', array(
            'name'      => 'http_post_url',
            'label'     => $this->__('Http Post URL'),
            'title'     => $this->__('Http Post URL'),
            'required'  => true,
            'class'     => 'validate-url'
        ));

        $form->setUseContainer(true);
        $form->setValues($consumerData);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
