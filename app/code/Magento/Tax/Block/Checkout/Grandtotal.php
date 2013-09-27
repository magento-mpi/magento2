<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Subtotal Total Row Renderer
 */
class Magento_Tax_Block_Checkout_Grandtotal extends Magento_Checkout_Block_Total_Default
{
    /**
     * Path to template file
     *
     * @var string
     */
    protected $_template = 'checkout/grandtotal.phtml';

    /**
     * @var Magento_Tax_Model_Config
     */
    protected $_taxConfig;

    /**
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Sales_Model_Config $salesConfig
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Tax_Model_Config $taxConfig
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
        Magento_Tax_Model_Config $taxConfig,
        array $data = array()
    ) {
        $this->_taxConfig = $taxConfig;
        parent::__construct($catalogData, $coreData, $context, $salesConfig, $customerSession, $checkoutSession,
            $storeManager, $data);
    }

    /**
     * Check if we have include tax amount between grandtotal incl/excl tax
     *
     * @return bool
     */
    public function includeTax()
    {
        if ($this->getTotal()->getAddress()->getGrandTotal()) {
            return $this->_taxConfig->displayCartTaxWithGrandTotal($this->getStore());
        }
        return false;
    }

    /**
     * Get grandtotal exclude tax
     *
     * @return float
     */
    public function getTotalExclTax()
    {
        $excl = $this->getTotal()->getAddress()->getGrandTotal()-$this->getTotal()->getAddress()->getTaxAmount();
        $excl = max($excl, 0);
        return $excl;
    }
}
