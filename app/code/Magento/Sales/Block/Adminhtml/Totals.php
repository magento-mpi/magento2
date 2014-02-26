<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Adminhtml;

class Totals extends \Magento\Sales\Block\Order\Totals
{
    /**
     * @var \Magento\Sales\Helper\Admin
     */
    protected $_adminHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = array()
    ) {
        $this->_adminHelper = $adminHelper;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Format total value based on order currency
     *
     * @param   \Magento\Object $total
     * @return  string
     */
    public function formatValue($total)
    {
        if (!$total->getIsFormated()) {
            return $this->_adminHelper->displayPrices(
                $this->getOrder(),
                $total->getBaseValue(),
                $total->getValue()
            );
        }
        return $total->getValue();
    }

    /**
     * Initialize order totals array
     *
     * @return \Magento\Sales\Block\Order\Totals
     */
    protected function _initTotals()
    {
        $this->_totals = array();
        $this->_totals['subtotal'] = new \Magento\Object(array(
            'code'      => 'subtotal',
            'value'     => $this->getSource()->getSubtotal(),
            'base_value'=> $this->getSource()->getBaseSubtotal(),
            'label'     => __('Subtotal')
        ));

        /**
         * Add shipping
         */
        if (!$this->getSource()->getIsVirtual() && ((float) $this->getSource()->getShippingAmount() || $this->getSource()->getShippingDescription()))
        {
            $this->_totals['shipping'] = new \Magento\Object(array(
                'code'      => 'shipping',
                'value'     => $this->getSource()->getShippingAmount(),
                'base_value'=> $this->getSource()->getBaseShippingAmount(),
                'label' => __('Shipping & Handling')
            ));
        }

        /**
         * Add discount
         */
        if (((float)$this->getSource()->getDiscountAmount()) != 0) {
            if ($this->getSource()->getDiscountDescription()) {
                $discountLabel = __('Discount (%1)', $this->getSource()->getDiscountDescription());
            } else {
                $discountLabel = __('Discount');
            }
            $this->_totals['discount'] = new \Magento\Object(array(
                'code'      => 'discount',
                'value'     => $this->getSource()->getDiscountAmount(),
                'base_value'=> $this->getSource()->getBaseDiscountAmount(),
                'label'     => $discountLabel
            ));
        }

        $this->_totals['grand_total'] = new \Magento\Object(array(
            'code'      => 'grand_total',
            'strong'    => true,
            'value'     => $this->getSource()->getGrandTotal(),
            'base_value'=> $this->getSource()->getBaseGrandTotal(),
            'label'     => __('Grand Total'),
            'area'      => 'footer'
        ));

        return $this;
    }
}
