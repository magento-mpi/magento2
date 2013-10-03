<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer balance block for order
 *
 */
namespace Magento\CustomerBalance\Block\Sales\Order;

class Customerbalance extends \Magento\Core\Block\Template
{
    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Initialize customer balance order total
     *
     * @return \Magento\CustomerBalance\Block\Sales\Order\Customerbalance
     */
    public function initTotals()
    {
        if ((float)$this->getSource()->getCustomerBalanceAmount() == 0) {
            return $this;
        }
        $total = new \Magento\Object(array(
            'code'      => $this->getNameInLayout(),
            'block_name'=> $this->getNameInLayout(),
            'area'      => $this->getArea()
        ));
        $after = $this->getAfterTotal();
        if (!$after) {
            $after = 'giftcards';
        }
        $this->getParentBlock()->addTotal($total, $after);
        return $this;
    }

    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }
}
