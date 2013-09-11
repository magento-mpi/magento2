<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales order view items block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Order\Creditmemo;

class Items extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return \Mage::registry('current_order');
    }

    public function getPrintCreditmemoUrl($creditmemo)
    {
        return \Mage::getUrl('*/*/printCreditmemo', array('creditmemo_id' => $creditmemo->getId()));
    }

    public function getPrintAllCreditmemosUrl($order)
    {
        return \Mage::getUrl('*/*/printCreditmemo', array('order_id' => $order->getId()));
    }

    /**
     * Get creditmemo totals block html
     *
     * @param   \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return  string
     */
    public function getTotalsHtml($creditmemo)
    {
        $totals = $this->getChildBlock('creditmemo_totals');
        $html = '';
        if ($totals) {
            $totals->setCreditmemo($creditmemo);
            $html = $totals->toHtml();
        }
        return $html;
    }

    /**
     * Get html of creditmemo comments block
     *
     * @param   \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return  string
     */
    public function getCommentsHtml($creditmemo)
    {
        $html = '';
        $comments = $this->getChildBlock('creditmemo_comments');
        if ($comments) {
            $comments->setEntity($creditmemo)
                ->setTitle(__('About Your Refund'));
            $html = $comments->toHtml();
        }
        return $html;
    }
}
