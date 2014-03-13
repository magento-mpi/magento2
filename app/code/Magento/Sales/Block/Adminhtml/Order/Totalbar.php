<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Order;

/**
 * Adminhtml creditmemo bar
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Totalbar extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /**
     * Totals
     *
     * @var array
     */
    protected $_totals = array();

    /**
     * Retrieve required options from parent
     *
     * @return void
     * @throws \Magento\Core\Exception
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new \Magento\Core\Exception(__('Please correct the parent block for this block.'));
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
        $this->_totals[] = array(
            'label' => $label,
            'value' => $value,
            'grand' => $grand
        );
        return $this;
    }
}
