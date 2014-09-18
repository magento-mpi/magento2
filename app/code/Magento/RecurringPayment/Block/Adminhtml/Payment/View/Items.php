<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Adminhtml\Payment\View;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Adminhtml recurring payment items grid
 */
class Items extends \Magento\Sales\Block\Adminhtml\Items\AbstractItems
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Service\V1\StockItemService $stockItemService
     * @param \Magento\Framework\Registry $registry
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Service\V1\StockItemService $stockItemService,
        \Magento\Framework\Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        array $data = array()
    ) {
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $stockItemService, $registry, $data);
    }


    /**
     * Retrieve required options from parent
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new \Magento\Framework\Model\Exception(__('Invalid parent block for this block'));
        }
        parent::_beforeToHtml();
    }

    /**
     * Return current recurring payment
     *
     * @return \Magento\RecurringPayment\Model\Payment
     */
    public function _getRecurringPayment()
    {
        return $this->_coreRegistry->registry('current_recurring_payment');
    }

    /**
     * Retrieve recurring payment item
     *
     * @return \Magento\Sales\Model\Order\Item
     */
    public function getItem()
    {
        return $this->_getRecurringPayment()->getItem();
    }

    /**
     * Retrieve formatted price
     *
     * @param   float $value
     * @return  string
     */
    public function formatPrice($value)
    {
        $store = $this->_storeManager->getStore($this->_getRecurringPayment()->getStore());
        return $this->priceCurrency->format(
            $value,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $store
        );
    }
}
