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
namespace Magento\Checkout\Block\Multishipping;

class Success extends \Magento\Checkout\Block\Multishipping\AbstractMultishipping
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Checkout\Model\Type\Multishipping $multishipping
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Checkout\Model\Type\Multishipping $multishipping,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $coreData, $multishipping, $data);
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
