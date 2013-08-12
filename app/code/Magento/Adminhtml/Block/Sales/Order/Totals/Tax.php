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
class Magento_Adminhtml_Block_Sales_Order_Totals_Tax extends Mage_Tax_Block_Sales_Order_Tax
{
    /**
     * @var Mage_Tax_Helper_Data
     */
    protected $_taxHelper;

    /**
     * @var Mage_Tax_Model_Calculation
     */
    protected $_taxCalculation;

    /**
     * @var Mage_Tax_Model_Sales_Order_Tax_Factory
     */
    protected $_taxOrderFactory;

    /**
     * Initialize dependencies
     *
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Tax_Model_Config $taxConfig
     * @param Mage_Tax_Helper_Data $taxHelper
     * @param Mage_Tax_Model_Calculation $taxCalculation
     * @param Mage_Tax_Model_Sales_Order_Tax_Factory $taxOrderFactory
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Tax_Model_Config $taxConfig,
        Mage_Tax_Helper_Data $taxHelper,
        Mage_Tax_Model_Calculation $taxCalculation,
        Mage_Tax_Model_Sales_Order_Tax_Factory $taxOrderFactory,
        array $data = array()
    ) {
        $this->_taxHelper = $taxHelper;
        $this->_taxCalculation = $taxCalculation;
        $this->_taxOrderFactory = $taxOrderFactory;
        parent::__construct($context, $taxConfig, $data);
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
        return Mage::app()->getStore();
    }
}
