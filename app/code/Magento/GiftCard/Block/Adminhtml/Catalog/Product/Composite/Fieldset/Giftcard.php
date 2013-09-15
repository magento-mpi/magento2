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
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        Magento_Core_Model_Registry $registry,
        \Magento\Core\Model\StoreManager $storeManager,
        array $data = array()
    ) {
        parent::__construct($registry, $taxData, $catalogData, $coreData, $context, $data);
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
     * @param null|string|bool|int|\Magento\Core\Model\Store $storeId $storeId
     * @return string
     */
    public function getCurrentCurrencyCode($storeId)
    {
        return $this->_storeManager->getStore($storeId)->getCurrentCurrencyCode();
    }
}
