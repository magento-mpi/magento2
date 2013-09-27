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
 * Adminhtml order creating gift message item form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Magento_Adminhtml_Block_Sales_Order_Create_Giftmessage_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * Entity for editing of gift message
     *
     * @var Magento_Eav_Model_Entity_Abstract
     */
    protected $_entity;

    /**
     * Giftmessage object
     *
     * @var Magento_GiftMessage_Model_Message
     */
    protected $_giftMessage;

    /**
     * @var Magento_Adminhtml_Model_Session_Quote
     */
    protected $_sessionQuote;

    /**
     * @param Magento_Adminhtml_Model_Session_Quote $sessionQuote
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Model_Session_Quote $sessionQuote,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_sessionQuote = $sessionQuote;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Set entity for form
     *
     * @param Magento_Object $entity
     * @return Magento_Adminhtml_Block_Sales_Order_Create_Giftmessage_Form
     */
    public function setEntity(Magento_Object $entity)
    {
        $this->_entity  = $entity;
        return $this;
    }

    /**
     * Retrive entity for form
     *
     * @return Magento_Object
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    protected function _getSession()
    {
        return $this->_sessionQuote;
    }

    /**
     * Retrieve default value for giftmessage sender
     *
     * @return string
     */
    public function getDefaultSender()
    {
        if(!$this->getEntity()) {
            return '';
        }

        if($this->_getSession()->getCustomer()->getId()) {
            return $this->_getSession()->getCustomer()->getName();
        }

        $object = $this->getEntity();

        if ($this->getEntity()->getQuote()) {
            $object = $this->getEntity()->getQuote();
        }

        return $object->getBillingAddress()->getName();
    }

    /**
     * Retrieve default value for giftmessage recipient
     *
     * @return string
     */
    public function getDefaultRecipient()
    {
        if(!$this->getEntity()) {
            return '';
        }

        $object = $this->getEntity();

        if ($this->getEntity()->getOrder()) {
            $object = $this->getEntity()->getOrder();
        }
        else if ($this->getEntity()->getQuote()){
            $object = $this->getEntity()->getQuote();
        }

        if ($object->getShippingAddress()) {
            return $object->getShippingAddress()->getName();
        }
        else if ($object->getBillingAddress()) {
            return $object->getBillingAddress()->getName();
        }

        return '';
    }

    /**
     * Prepares form
     *
     * @return Magento_Adminhtml_Block_Sales_Order_Create_Giftmessage_Form
     */
    public function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('main', array('no_container'=>true));

        $fieldset->addField('type','hidden',
            array(
                'name' =>  $this->_getFieldName('type'),
            )
        );

        $form->setHtmlIdPrefix($this->_getFieldIdPrefix());

        if ($this->getEntityType() == 'item') {
            $this->_prepareHiddenFields($fieldset);
        } else {
            $this->_prepareVisibleFields($fieldset);
        }

        // Set default sender and recipient from billing and shipping adresses
        if(!$this->getMessage()->getSender()) {
            $this->getMessage()->setSender($this->getDefaultSender());
        }

        if(!$this->getMessage()->getRecipient()) {
            $this->getMessage()->setRecipient($this->getDefaultRecipient());
        }

        $this->getMessage()->setType($this->getEntityType());

        // Overriden default data with edited when block reloads througth Ajax
        $this->_applyPostData();

        $form->setValues($this->getMessage()->getData());

        $this->setForm($form);
        return $this;
    }

    /**
     * Prepare form fieldset
     * All fields are hidden
     *
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     *
     * @return Magento_Adminhtml_Block_Sales_Order_Create_Giftmessage_Form
     */
    protected function _prepareHiddenFields(Magento_Data_Form_Element_Fieldset $fieldset)
    {
        $fieldset->addField('sender', 'hidden',
            array(
                'name' => $this->_getFieldName('sender')
            )
        );
        $fieldset->addField('recipient', 'hidden',
            array(
                'name' => $this->_getFieldName('recipient')
            )
        );

        $fieldset->addField('message', 'hidden',
            array(
                'name' => $this->_getFieldName('message')
            )
        );
        return $this;
    }

    /**
     * Prepare form fieldset
     * All fields are visible
     *
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     *
     * @return Magento_Adminhtml_Block_Sales_Order_Create_Giftmessage_Form
     */
    protected function _prepareVisibleFields(Magento_Data_Form_Element_Fieldset $fieldset)
    {
        $fieldset->addField('sender', 'text',
            array(
                'name'     => $this->_getFieldName('sender'),
                'label'    => __('From'),
                'required' => $this->getMessage()->getMessage() ? true : false
            )
        );
        $fieldset->addField('recipient', 'text',
            array(
                'name'     => $this->_getFieldName('recipient'),
                'label'    => __('To'),
                'required' => $this->getMessage()->getMessage() ? true : false
            )
        );

        $fieldset->addField('message', 'textarea',
            array(
                'name'      => $this->_getFieldName('message'),
                'label'     => __('Message'),
                'rows'      => '5',
                'cols'      => '20',
            )
        );
        return $this;
    }

    /**
     * Initialize gift message for entity
     *
     * @return Magento_Adminhtml_Block_Sales_Order_Create_Giftmessage_Form
     */
    protected function _initMessage()
    {
        $this->_giftMessage = $this->helper('Magento_GiftMessage_Helper_Message')->getGiftMessage(
                                   $this->getEntity()->getGiftMessageId()
                              );
        return $this;
    }

    /**
     * Retrive gift message for entity
     *
     * @return Magento_GiftMessage_Model_Message
     */
    public function getMessage()
    {
        if(is_null($this->_giftMessage)) {
            $this->_initMessage();
        }

        return $this->_giftMessage;
    }

    /**
     * Retrive real name for field
     *
     * @param string $name
     * @return string
     */
    protected  function _getFieldName($name)
    {
        return 'giftmessage[' . $this->getEntity()->getId() . '][' . $name . ']';
    }

    /**
     * Retrive real html id for field
     *
     * @param string $name
     * @return string
     */
    protected  function _getFieldId($id)
    {
        return $this->_getFieldIdPrefix() . $id;
    }

    /**
     * Retrive field html id prefix
     *
     * @return unknown
     */
    protected  function _getFieldIdPrefix()
    {
        return 'giftmessage_' . $this->getEntity()->getId() . '_';
    }

    /**
     * Aplies posted data to gift message
     *
     * @return Magento_Adminhtml_Block_Sales_Order_Create_Giftmessage_Form
     */
    protected function _applyPostData()
    {
        if(is_array($giftmessages = $this->getRequest()->getParam('giftmessage'))
           && isset($giftmessages[$this->getEntity()->getId()])) {
            $this->getMessage()->addData($giftmessages[$this->getEntity()->getId()]);
        }

        return $this;
    }

}
