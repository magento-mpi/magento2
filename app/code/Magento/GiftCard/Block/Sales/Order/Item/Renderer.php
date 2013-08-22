<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCard_Block_Sales_Order_Item_Renderer extends Magento_Sales_Block_Order_Item_Renderer_Default
{
    /**
     * Prepare custom option for display, returns false if there's no value
     *
     * @param string $code
     * @return mixed
     */
    protected function _prepareCustomOption($code)
    {
        if ($option = $this->getOrderItem()->getProductOptionByCode($code)) {
            return $this->escapeHtml($option);
        }
        return false;
    }

    /**
     * Prepare a string containing name and email
     *
     * @param string $name
     * @param string $email
     * @return mixed
     */
    protected function _getNameEmailString($name, $email)
    {
        return "$name &lt;{$email}&gt;";
    }

    /**
     * Get gift card option list
     *
     * @return array
     */
    protected function _getGiftcardOptions()
    {
        $result = array();
        if ($value = $this->_prepareCustomOption('giftcard_sender_name')) {
            if ($email = $this->_prepareCustomOption('giftcard_sender_email')) {
                $value = $this->_getNameEmailString($value, $email);
            }
            $result[] = array(
                'label'=>__('Gift Card Sender'),
                'value'=>$value,
            );
        }
        if ($value = $this->_prepareCustomOption('giftcard_recipient_name')) {
            if ($email = $this->_prepareCustomOption('giftcard_recipient_email')) {
                $value = $this->_getNameEmailString($value, $email);
            }
            $result[] = array(
                'label'=>__('Gift Card Recipient'),
                'value'=>$value,
            );
        }
        if ($value = $this->_prepareCustomOption('giftcard_message')) {
            $result[] = array(
                'label'=>__('Gift Card Message'),
                'value'=>$value,
            );
        }
        return $result;
    }

    /**
     * Return gift card and custom options array
     *
     * @return array
     */
    public function getItemOptions()
    {
        return array_merge($this->_getGiftcardOptions(), parent::getItemOptions());
    }
}
