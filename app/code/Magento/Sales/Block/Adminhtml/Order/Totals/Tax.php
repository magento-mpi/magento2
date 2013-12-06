<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml order tax totals block
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Order\Totals;

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
     * @var \Magento\Tax\Model\Sales\Order\TaxFactory
     */
    protected $_taxOrderFactory;

    /**
     * @var \Magento\Sales\Helper\Admin
     */
    protected $_salesAdminHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Tax\Model\Sales\Order\TaxFactory $taxOrderFactory
     * @param \Magento\Sales\Helper\Admin $salesAdminHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Tax\Model\Sales\Order\TaxFactory $taxOrderFactory,
        \Magento\Sales\Helper\Admin $salesAdminHelper,
        array $data = array()
    ) {
        $this->_taxHelper = $taxHelper;
        $this->_taxCalculation = $taxCalculation;
        $this->_taxOrderFactory = $taxOrderFactory;
        $this->_salesAdminHelper = $salesAdminHelper;
        parent::__construct($context, $taxConfig, $data);
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
        return $this->_salesAdminHelper->displayPrices(
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
        return $this->_storeManager->getStore();
    }
}
