<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Block\Adminhtml\Sales\Items\Column\Name;

class Giftcard
    extends \Magento\Adminhtml\Block\Sales\Items\Column\Name
{
    /**
     * Prepare custom option for display, returns false if there's no value
     *
     * @param string $code
     * @return mixed
     */
    protected function _prepareCustomOption($code)
    {
        if ($option = $this->getItem()->getProductOptionByCode($code)) {
            return $this->escapeHtml($option);
        }
        return false;
    }

    /**
     * Get gift card option list
     *
     * @return array
     */
    protected function _getGiftcardOptions()
    {
        $result = array();
        if ($type = $this->getItem()->getProductOptionByCode('giftcard_type')) {
            switch ($type) {
                case \Magento\GiftCard\Model\Giftcard::TYPE_VIRTUAL:
                    $type = __('Virtual');
                    break;
                case \Magento\GiftCard\Model\Giftcard::TYPE_PHYSICAL:
                    $type = __('Physical');
                    break;
                case \Magento\GiftCard\Model\Giftcard::TYPE_COMBINED:
                    $type = __('Combined');
                    break;
            }

            $result[] = array(
                'label'=>__('Gift Card Type'),
                'value'=>$type,
            );
        }


        if ($value = $this->_prepareCustomOption('giftcard_sender_name')) {
            if ($email = $this->_prepareCustomOption('giftcard_sender_email')) {
                $value = "{$value} &lt;{$email}&gt;";
            }
            $result[] = array(
                'label'=>__('Gift Card Sender'),
                'value'=>$value,
                'custom_view'=>true,
            );
        }
        if ($value = $this->_prepareCustomOption('giftcard_recipient_name')) {
            if ($email = $this->_prepareCustomOption('giftcard_recipient_email')) {
                $value = "{$value} &lt;{$email}&gt;";
            }
            $result[] = array(
                'label'=>__('Gift Card Recipient'),
                'value'=>$value,
                'custom_view'=>true,
            );
        }
        if ($value = $this->_prepareCustomOption('giftcard_message')) {
            $result[] = array(
                'label'=>__('Gift Card Message'),
                'value'=>$value,
            );
        }

        if ($value = $this->_prepareCustomOption('giftcard_lifetime')) {
            $result[] = array(
                'label'=>__('Gift Card Lifetime'),
                'value'=>sprintf('%s days', $value),
            );
        }

        $yes = __('Yes');
        $no = __('No');
        if ($value = $this->_prepareCustomOption('giftcard_is_redeemable')) {
            $result[] = array(
                'label'=>__('Gift Card Is Redeemable'),
                'value'=>($value ? $yes : $no),
            );
        }

        $createdCodes = 0;
        $totalCodes = $this->getItem()->getQtyOrdered();
        if ($codes = $this->getItem()->getProductOptionByCode('giftcard_created_codes')) {
            $createdCodes = count($codes);
        }

        if (is_array($codes)) {
            foreach ($codes as &$code) {
                if ($code === null) {
                    $code = __('We cannot create this gift card.');
                }
            }
        } else {
            $codes = array();
        }

        for ($i = $createdCodes; $i < $totalCodes; $i++) {
            $codes[] = __('N/A');
        }

        $result[] = array(
            'label'=>__('Gift Card Accounts'),
            'value'=>implode('<br />', $codes),
            'custom_view'=>true,
        );



        return $result;
    }

    /**
     * Return gift card and custom options array
     *
     * @return array
     */
    public function getOrderOptions()
    {
        return array_merge($this->_getGiftcardOptions(), parent::getOrderOptions());
    }
}
