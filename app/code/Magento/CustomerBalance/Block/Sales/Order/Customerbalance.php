<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Block\Sales\Order;

/**
 * Customer balance block for order
 *
 */
class Customerbalance extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Initialize customer balance order total
     *
     * @return $this
     */
    public function initTotals()
    {
        if ((double)$this->getSource()->getCustomerBalanceAmount() == 0) {
            return $this;
        }
        $total = new \Magento\Framework\Object(
            array(
                'code' => $this->getNameInLayout(),
                'block_name' => $this->getNameInLayout(),
                'area' => $this->getArea()
            )
        );
        $after = $this->getAfterTotal();
        if (!$after) {
            $after = 'giftcards';
        }
        $this->getParentBlock()->addTotal($total, $after);
        return $this;
    }

    /**
     * @return string
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return string
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }
}
