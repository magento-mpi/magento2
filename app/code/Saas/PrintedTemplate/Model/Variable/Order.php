<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Container for Magento_Sales_Model_Order for order variable
 *
 * Container that can restrict access to properties and method
 * with white list.
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Variable_Order extends Saas_PrintedTemplate_Model_Variable_Abstract
{
    /**
     * Cache for _getGiftMessage()
     *
     * @var Magento_GiftMessage_Model_Message|null
     */
    protected $_giftMessage;

    /**
     * Constructor
     *
     * @see Saas_PrintedTemplate_Model_Template_Variable_Abstract::__construct()
     * @param Magento_Sales_Model_Order $value Order
     */
    public function __construct(Magento_Sales_Model_Order $value)
    {
        parent::__construct($value);
        $this->_setListsFromConfig('order');
    }

    /**
     * Return table with gift cards
     *
     * @return string HTML
     */
    public function getGiftCards()
    {
        return Mage::getBlockSingleton('Saas_PrintedTemplate_Block_GiftCards')
            ->setOrder($this->_value)
            ->toHtml();
    }

    /**
     * Return table with gift cards with amounts in base price
     *
     * @return string HTML
     */
    public function getBaseGiftCards()
    {
        return Mage::getBlockSingleton('Saas_PrintedTemplate_Block_GiftCards')
            ->setOrder($this->_value)
            ->setUseBaseAmount(true)
            ->toHtml();
    }

    /**
     * Tries to load giftmessage/message by ID from the order
     *
     * @return  Magento_GiftMessage_Model_Message|null
     */
    protected function _getGiftMessage()
    {
        if (!$this->_giftMessage && $this->_value->getGiftMessageId()) {
            $message = Mage::getModel('Magento_GiftMessage_Model_Message')->load($this->_value->getGiftMessageId());
            if ($message->getId()) {
                $this->_giftMessage = $message;
            }
        }

        return $this->_giftMessage;
    }

    /**
     * Returns sender of gift message
     *
     * @return string
     */
    public function getGiftMessageSender()
    {
        if ($this->_getGiftMessage()) {
            return $this->_getGiftMessage()->getSender();
        }
    }

    /**
     * Returns recipient of gift message
     *
     * @return string
     */
    public function getGiftMessageRecipient()
    {
        if ($this->_getGiftMessage()) {
            return $this->_getGiftMessage()->getRecipient();
        }
    }

    /**
     * Returns text of gift message
     *
     * @return string
     */
    public function getGiftMessageText()
    {
        if ($this->_getGiftMessage()) {
            return $this->_getGiftMessage()->getMessage();
        }
    }

    /**
     * Return status label of order
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->_value->getStatusLabel();
    }

    /**
     * Formats currency using order formater
     *
     * @param float
     * @return string
     */
    public function formatCurrency($value)
    {
        return (null !== $value) ? $this->_value->formatPriceTxt($value) : '';
    }

    /**
     * Formats currency using order formater
     *
     * @param float
     * @return string
     */
    public function formatBaseCurrency($value)
    {
        return $this->_value->formatBasePrice($value);
    }
}
