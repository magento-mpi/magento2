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
namespace Magento\Adminhtml\Block\Sales\Order\Totals;

class Tax extends \Magento\Tax\Block\Sales\Order\Tax
{
    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxHelper;

    /**
     * @var \Magento\Tax\Model\Calculation
     */
    protected $_taxCalculation;

    /**
     * @var \Magento\Tax\Model\Sales\Order\Tax\Factory
     */
    protected $_taxOrderFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Tax\Model\Sales\Order\Tax\Factory $taxOrderFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Tax\Model\Sales\Order\Tax\Factory $taxOrderFactory,
        array $data = array()
    ) {
        $this->_taxHelper = $taxHelper;
        $this->_taxCalculation = $taxCalculation;
        $this->_taxOrderFactory = $taxOrderFactory;
        parent::__construct($coreData, $context, $taxConfig, $data);
    }

    /**
     * Get full information about taxes applied to order
     *
     * @return array
     */
    public function getFullTaxInfo()
    {
        /** @var $source \Magento\Sales\Model\Order */
        $source = $this->getOrder();
        $taxClassAmount = array();
        if ($source instanceof \Magento\Sales\Model\Order) {
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
        return $this->_helperFactory->get('Magento\Adminhtml\Helper\Sales')->displayPrices(
            $this->getSource(), $baseAmount, $amount, false, '<br />'
        );
    }

    /**
     * Get store object for process configuration settings
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        return \Mage::app()->getStore();
    }
}
