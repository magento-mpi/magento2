<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Sales\Block\Adminhtml\Order;

/**
 * Adminhtml creditmemo bar
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Totalbar extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /**
     * Totals
     *
     * @var array
     */
    protected $_totals = [];

    /**
     * Retrieve required options from parent
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new \Magento\Framework\Model\Exception(__('Please correct the parent block for this block.'));
        }
        $this->setOrder($this->getParentBlock()->getOrder());
        $this->setSource($this->getParentBlock()->getSource());
        $this->setCurrency($this->getParentBlock()->getOrder()->getOrderCurrency());

        foreach ($this->getParentBlock()->getOrderTotalbarData() as $v) {
            $this->addTotal($v[0], $v[1], $v[2]);
        }

        parent::_beforeToHtml();
    }

    /**
     * Get totals
     *
     * @return array
     */
    protected function getTotals()
    {
        return $this->_totals;
    }

    /**
     * Add total
     *
     * @param string $label
     * @param float $value
     * @param bool $grand
     * @return $this
     */
    public function addTotal($label, $value, $grand = false)
    {
        $this->_totals[] = ['label' => $label, 'value' => $value, 'grand' => $grand];
        return $this;
    }
}
