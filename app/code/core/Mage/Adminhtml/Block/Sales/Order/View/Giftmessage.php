<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Edit order giftmessage block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Ivan Chepurnyi <mitch@varien.com>
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
     * Giftmessage object
     *
     * @var Mage_GiftMessage_Model_Message
     */
    protected $_giftMessage;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sales/order/view/giftmessage.phtml');
    }

    /**
     * Prepares layout of block
     *
     * @return Mage_Adminhtml_Block_Sales_Order_View_Giftmessage
     */
    protected function _prepareLayout()
    {
        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'   => Mage::helper('giftmessage')->__('Save Gift Message'),
                    'class'   => 'save'
                ))
        );

        return $this;
    }

    /**
     * Retrive save button html
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        $this->getChild('save_button')->setOnclick(
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
            $this->setEntity(Mage::getModel('giftmessage/message')->getEntityModelByType('order'));
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
            return $this->getEntity()->getOrder()->getShippingAddress()->getName();
        }

        return $this->getEntity()->getShippingAddress()->getName();
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
        return 'giftmessage_' . $this->getEntity()->getId() . '_';
    }

    /**
     * Initialize gift message for entity
     *
     * @return Mage_Adminhtml_Block_Sales_Order_View_Giftmessage
     */
    protected function _initMessage()
    {
        $this->_giftMessage = $this->helper('giftmessage/message')->getGiftMessage(
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
        return $this->helper('giftmessage/message')->getIsMessagesAvailable(
            'order', $this->getEntity(), $this->getEntity()->getStoreId()
        );
    }

} // Class Mage_Adminhtml_Block_Sales_Order_View_Giftmessage End