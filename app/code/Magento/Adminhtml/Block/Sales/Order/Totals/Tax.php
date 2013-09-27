<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml order tax totals block
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Totals_Tax extends Magento_Tax_Block_Sales_Order_Tax
{
    /**
     * @var Magento_Tax_Helper_Data
     */
    protected $_taxHelper;

    /**
     * @var Magento_Tax_Model_Calculation
     */
    protected $_taxCalculation;

    /**
     * @var Magento_Tax_Model_Sales_Order_Tax_Factory
     */
    protected $_taxOrderFactory;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Tax_Model_Config $taxConfig
     * @param Magento_Tax_Helper_Data $taxHelper
     * @param Magento_Tax_Model_Calculation $taxCalculation
     * @param Magento_Tax_Model_Sales_Order_Tax_Factory $taxOrderFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Tax_Model_Config $taxConfig,
        Magento_Tax_Helper_Data $taxHelper,
        Magento_Tax_Model_Calculation $taxCalculation,
        Magento_Tax_Model_Sales_Order_Tax_Factory $taxOrderFactory,
        array $data = array()
    ) {
        $this->_taxHelper = $taxHelper;
        $this->_taxCalculation = $taxCalculation;
        $this->_taxOrderFactory = $taxOrderFactory;
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($coreData, $context, $taxConfig, $data);
    }

    /**
     * Get full information about taxes applied to order
     *
     * @return array
     */
    public function getFullTaxInfo()
    {
        /** @var $source Magento_Sales_Model_Order */
        $source = $this->getOrder();
        $taxClassAmount = array();
        if ($source instanceof Magento_Sales_Model_Order) {
            $taxClassAmount = $this->_taxHelper->getCalculatedTaxes($source);
            $shippingTax    = $this->_taxHelper->getShippingTax($source);
            $taxClassAmount = array_merge($taxClassAmount, $shippingTax);
            if (empty($taxClassAmount)) {
                $rates = $this->_taxOrderFactory->create()->getCollection()->loadByOrder($source)->toArray();
                $taxClassAmount =  $this->_taxCalculation->reproduceProcess($rates['items']);
            }
        }
        return $taxClassAmount;
    }

    /**
     * Display tax amount
     *
     * @param string $amount
     * @param string $baseAmount
     * @return string
     */
    public function displayAmount($amount, $baseAmount)
    {
        return $this->_helperFactory->get('Magento_Adminhtml_Helper_Sales')->displayPrices(
            $this->getSource(), $baseAmount, $amount, false, '<br />'
        );
    }

    /**
     * Get store object for process configuration settings
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }
}
