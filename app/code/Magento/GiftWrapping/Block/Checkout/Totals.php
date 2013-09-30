<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping total block for checkout
 *
 */
class Magento_GiftWrapping_Block_Checkout_Totals extends Magento_Checkout_Block_Total_Default
{
    /**
     * Template file path
     *
     * @var string
     */
    protected $_template = 'checkout/totals.phtml';

    /**
     * Gift wrapping data
     *
     * @var Magento_GiftWrapping_Helper_Data
     */
    protected $_giftWrappingData = null;

    /**
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Sales_Model_Config $salesConfig
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_GiftWrapping_Helper_Data $giftWrappingData
     * @param Magento_Sales_Model_Config $salesConfig
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Sales_Model_Config $salesConfig,
        Magento_Customer_Model_Session $customerSession,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_GiftWrapping_Helper_Data $giftWrappingData,

        array $data = array()
    ) {
        $this->_giftWrappingData = $giftWrappingData;
        parent::__construct($catalogData, $coreData, $context, $salesConfig, $customerSession, $checkoutSession,
            $storeManager, $data);
    }

    /**
     * Return information for showing
     *
     * @return array
     */
    public function getValues()
    {
        $values = array();
        $total = $this->getTotal();
        $totals = $this->_giftWrappingData->getTotals($total);
        foreach ($totals as $total) {
            $values[$total['label']] = $total['value'];
        }
        return $values;
    }
}
