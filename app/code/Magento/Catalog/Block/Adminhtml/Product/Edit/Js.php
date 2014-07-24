<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Adminhtml\Product\Edit;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Tax\Service\V1\TaxCalculationServiceInterface;
use Magento\Tax\Model\TaxClass\Source\Product as ProductTaxClassSource;

class Js extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var TaxCalculationServiceInterface
     */
    protected $calculationService;

    /**
     * @var ProductTaxClassSource
     */
    protected $productTaxClassSource;

    /**
     * Current customer
     *
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CurrentCustomer $currentCustomer
     * @param \Magento\Core\Helper\Data $coreHelper
     * @param TaxCalculationServiceInterface $calculationService
     * @param ProductTaxClassSource $productTaxClassSource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        CurrentCustomer $currentCustomer,
        \Magento\Core\Helper\Data $coreHelper,
        TaxCalculationServiceInterface $calculationService,
        ProductTaxClassSource $productTaxClassSource,
        array $data = array()
    ) {
        $this->coreRegistry = $registry;
        $this->currentCustomer = $currentCustomer;
        $this->coreHelper = $coreHelper;
        $this->calculationService = $calculationService;
        $this->productTaxClassSource = $productTaxClassSource;
        parent::__construct($context, $data);
    }

    /**
     * Get currently edited product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('current_product');
    }

    /**
     * Get store object of curently edited product
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        $product = $this->getProduct();
        if ($product) {
            return $this->_storeManager->getStore($product->getStoreId());
        }
        return $this->_storeManager->getStore();
    }

    /**
     * Get all tax rates JSON for all product tax classes.
     *
     * @return string
     */
    public function getAllRatesByProductClassJson()
    {
        $result = array();
        foreach ($this->productTaxClassSource->getAllOptions() as $productTaxClass) {
            $taxClassId = $productTaxClass['value'];
            $taxRate = $this->calculationService->getDefaultCalculatedRate(
                $taxClassId,
                $this->currentCustomer->getCustomerId(),
                $this->getStore()->getId()
            );
            $result["value_{$taxClassId}"] = $taxRate;
        }
        return $this->coreHelper->jsonEncode($result);
    }
}
