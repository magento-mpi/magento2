<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Edit order giftmessage block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_View_Giftmessage extends Mage_Adminhtml_Block_Widget
{
    /**
     * Entity for editing of gift message
     *
     * @var Mage_Eav_Model_Entity_Abstract
     */
    protected $_entity;

    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Giftmessage object
     *
     * @var Mage_GiftMessage_Model_Message
     */
    protected $_giftMessage;

    protected function _beforeToHtml()
    {
        if ($this->getParentBlock() && ($order = $this->getOrder())) {
            $this->setEntity($order);
        }
        parent::_beforeToHtml();
    }

    /**
     * Prepares layout of block
     *
     * @return Mage_Adminhtml_Block_Sales_Order_View_Giftmessage
     */
    protected function _prepareLayout()
    {
        $this->addChild('save_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label'   => Mage::helper('Mage_GiftMessage_Helper_Data')->__('Save Gift Message'),
            'class'   => 'save'
        ));

        return $this;
    }

    /**
     * Retrive save button html
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        $this->getChildBlock('save_button')->setOnclick(
            'giftMessagesController.saveGiftMessage(\''. $this->getHtmlId() .'\')'
        );

        return $this->getChildHtml('save_button');
    }

    /**
     * Set entity for form
     *
     * @param Varien_Object $entity
     * @return Mage_Adminhtml_Block_Sales_Order_View_Giftmessage
     */
    public function setEntity(Varien_Object $entity)
    {
        $this->_entity  = $entity;
        return $this;
    }

    /**
     * Retrive entity for form
     *
     * @return Varien_Object
     */
    public function getEntity()
    {
        if(is_null($this->_entity)) {
            $this->setEntity(Mage::getModel('Mage_GiftMessage_Model_Message')->getEntityModelByType('order'));
            $this->getEntity()->load($this->getRequest()->getParam('entity'));
        }
        return $this->_entity;
    }

    /**
     * Retrive default value for giftmessage sender
     *
     * @return string
     */
    public function getDefaultSender()
    {
        if(!$this->getEntity()) {
            return '';
        }

        if($this->getEntity()->getOrder()) {
            return $this->getEntity()->getOrder()->getCustomerName();
        }

        return $this->getEntity()->getCustomerName();
    }

    /**
     * Retrive default value for giftmessage recipient
     *
     * @return string
     */
    public function getDefaultRecipient()
    {
        if(!$this->getEntity()) {
            return '';
        }

        if($this->getEntity()->getOrder()) {
            if ($this->getEntity()->getOrder()->getShippingAddress()) {
                return $this->getEntity()->getOrder()->getShippingAddress()->getName();
            } else if ($this->getEntity()->getOrder()->getBillingAddress()) {
                return $this->getEntity()->getOrder()->getBillingAddress()->getName();
            }
        }

        if ($this->getEntity()->getShippingAddress()) {
            return $this->getEntity()->getShippingAddress()->getName();
        } else if ($this->getEntity()->getBillingAddress()) {
            return $this->getEntity()->getBillingAddress()->getName();
        }

        return '';
    }

    /**
     * Retrive real name for field
     *
     * @param string $name
     * @return string
     */
    public function getFieldName($name)
    {
        return 'giftmessage[' . $this->getEntity()->getId() . '][' . $name . ']';
    }

    /**
     * Retrive real html id for field
     *
     * @param string $name
     * @return string
     */
    public function getFieldId($id)
    {
        return $this->getFieldIdPrefix() . $id;
    }

    /**
     * Retrive field html id prefix
     *
     * @return string
     */
    public function getFieldIdPrefix()
    {
        return 'giftmessage_order_' . $this->getEntity()->getId() . '_';
    }

    /**
     * Initialize gift message for entity
     *
     * @return Mage_Adminhtml_Block_Sales_Order_View_Giftmessage
     */
    protected function _initMessage()
    {
        $this->_giftMessage = $this->helper('Mage_GiftMessage_Helper_Message')->getGiftMessage(
                                   $this->getEntity()->getGiftMessageId()
                              );

        // init default values for giftmessage form
        if(!$this->getMessage()->getSender()) {
            $this->getMessage()->setSender($this->getDefaultSender());
        }
        if(!$this->getMessage()->getRecipient()) {
            $this->getMessage()->setRecipient($this->getDefaultRecipient());
        }

        return $this;
    }

    /**
     * Retrive gift message for entity
     *
     * @return Mage_GiftMessage_Model_Message
     */
    public function getMessage()
    {
        if(is_null($this->_giftMessage)) {
            $this->_initMessage();
        }

        return $this->_giftMessage;
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/sales_order_view_giftmessage/save',
            array(
                'entity'=>$this->getEntity()->getId(),
                'type'  =>'order',
                'reload' => 1
            )
        );
    }

    /**
     * Retrive block html id
     *
     * @return string
     */
    public function getHtmlId()
    {
        return substr($this->getFieldIdPrefix(), 0, -1);
    }

    /**
     * Indicates that block can display giftmessages form
     *
     * @return boolean
     */
    public function canDisplayGiftmessage()
    {
        return $this->helper('Mage_GiftMessage_Helper_Message')->getIsMessagesAvailable(
            'order', $this->getEntity(), $this->getEntity()->getStoreId()
        );
    }

}
