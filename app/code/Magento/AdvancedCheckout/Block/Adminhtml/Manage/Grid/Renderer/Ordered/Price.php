<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml grid product price column custom renderer for last ordered items
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Grid\Renderer\Ordered;

class Price
    extends \Magento\Adminhtml\Block\Sales\Order\Create\Search\Grid\Renderer\Price
{
    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Core\Model\LocaleInterface $locale,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_locale = $locale;
    }

    /**
     * Render price for last ordered item
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        // Show base price of product - the real price will be shown when user will configure product (if needed)
        $priceInitial = $row->getProduct()->getPrice() * 1;

        $priceInitial = floatval($priceInitial) * $this->_getRate($row);
        $priceInitial = sprintf("%f", $priceInitial);
        $currencyCode = $this->_getCurrencyCode($row);
        if ($currencyCode) {
            $priceInitial = $this->_locale->currency($currencyCode)->toCurrency($priceInitial);
        }

        return $priceInitial;
    }
}
