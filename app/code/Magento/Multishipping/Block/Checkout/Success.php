<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multishipping checkout success information
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Multishipping\Block\Checkout;

class Success extends \Magento\Multishipping\Block\Checkout\AbstractMultishipping
{
    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping,
        array $data = array()
    ) {
        parent::__construct($context, $multishipping, $data);
    }

    /**
     * @return array|bool|string
     */
    public function getOrderIds()
    {
        $ids = $this->_session->getOrderIds(true);
        if ($ids && is_array($ids)) {
            return $ids;
            return implode(', ', $ids);
        }
        return false;
    }

    /**
     * @param int $orderId
     * @return string
     */
    public function getViewOrderUrl($orderId)
    {
        return $this->getUrl('sales/order/view/', array('order_id' => $orderId, '_secure' => true));
    }

    /**
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}
