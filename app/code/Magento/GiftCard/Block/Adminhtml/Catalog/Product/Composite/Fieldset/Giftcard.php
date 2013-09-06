<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCard_Block_Adminhtml_Catalog_Product_Composite_Fieldset_Giftcard
    extends Magento_GiftCard_Block_Catalog_Product_View_Type_Giftcard
{
    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_StoreManager $storeManager,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
    }

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
     * @param null|string|bool|int|Magento_Core_Model_Store $storeId $storeId
     * @return string
     */
    public function getCurrentCurrencyCode($storeId)
    {
        return $this->_storeManager->getStore($storeId)->getCurrentCurrencyCode();
    }
}
