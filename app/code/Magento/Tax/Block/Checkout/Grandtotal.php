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
namespace Magento\Tax\Block\Checkout;

class Grandtotal extends \Magento\Checkout\Block\Total\DefaultTotal
{
    /**
     * Path to template file
     *
     * @var string
     */
    protected $_template = 'checkout/grandtotal.phtml';

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxConfig;

    /**
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Tax\Model\Config $taxConfig,
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
