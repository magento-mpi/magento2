<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Weee\Block\Sales\Order;

class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Weee\Helper\Data
     */
    protected $_weeeData;

    /**
     * @param \Magento\Weee\Helper\Data $_weeeData
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Weee\Helper\Data $_weeeData,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = array()
    ) {
        $this->_weeeData = $_weeeData;
        parent::__construct($context, $data);
    }

    /**
     * Get totals source object
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Create the weee ("FPT") totals summary
     *
     * @return $this
     */
    public function initTotals()
    {
        /** @var $items \Magento\Sales\Model\Order\Item[] */
        $items = $this->getSource()->getAllItems();
        $store = $this->getSource()->getStore();

        $weeeTotal = $this->_weeeData->getTotalAmounts($items, $store);
        if ($weeeTotal) {
            // Add our total information to the set of other totals
            $total = new \Magento\Framework\Object(
                array(
                    'code' => $this->getNameInLayout(),
                    'label' => __('FPT'),
                    'value' => $weeeTotal
                )
            );
            if ($this->getBeforeCondition()) {
                $this->getParentBlock()->addTotalBefore($total, $this->getBeforeCondition());
            } else {
                $this->getParentBlock()->addTotal($total, $this->getAfterCondition());
            }
        }
        return $this;
    }
}
