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
class Magento_Tax_Block_Checkout_Discount extends Magento_Checkout_Block_Total_Default
{
    /**
     * @var Magento_Tax_Model_Config
     */
    protected $_taxConfig;

    /**
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Tax_Model_Config $taxConfig
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Config $coreConfig,
        Magento_Tax_Model_Config $taxConfig,
        array $data = array()
    ) {
        $this->_taxConfig = $taxConfig;
        parent::__construct($catalogData, $coreData, $context, $coreConfig, $data);
    }

    /**
     * @return bool
     */
    public function displayBoth()
    {
        return $this->_taxConfig->displayCartSubtotalBoth();
    }
}
