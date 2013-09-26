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
 */
namespace Magento\Sales\Block\Order\Creditmemo;

class Items extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * @param object $creditmemo
     * @return string
     */
    public function getPrintCreditmemoUrl($creditmemo)
    {
        return $this->getUrl('*/*/printCreditmemo', array('creditmemo_id' => $creditmemo->getId()));
    }

    /**
     * @param object $order
     * @return string
     */
    public function getPrintAllCreditmemosUrl($order)
    {
        return $this->getUrl('*/*/printCreditmemo', array('order_id' => $order->getId()));
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
