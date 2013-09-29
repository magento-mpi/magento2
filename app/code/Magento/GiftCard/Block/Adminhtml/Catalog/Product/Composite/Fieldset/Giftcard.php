<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Block\Adminhtml\Catalog\Product\Composite\Fieldset;

class Giftcard
    extends \Magento\GiftCard\Block\Catalog\Product\View\Type\Giftcard
{
    /**
     * Checks whether block is last fieldset in popup
     *
     * @return bool
     */
    public function getIsLastFieldset()
    {
        if ($this->hasData('is_last_fieldset')) {
            return $this->getData('is_last_fieldset');
        } else {
            return !$this->getProduct()->getOptions();
        }
    }

    /**
     * Get current currency code
     *
     * @param null|string|bool|int|\Magento\Core\Model\Store $storeId $storeId
     * @return string
     */
    public function getCurrentCurrencyCode($storeId)
    {
        return $this->_storeManager->getStore($storeId)->getCurrentCurrencyCode();
    }
}
