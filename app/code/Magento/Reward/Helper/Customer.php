<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward Helper for operations with customer
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Helper;

class Customer extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Return Unsubscribe notification URL
     *
     * @param string|boolean $notification Notification type
     * @param int|string|\Magento\Core\Model\Store $storeId
     * @return string
     */
    public function getUnsubscribeUrl($notification = false, $storeId = null)
    {
        $params = array();

        if ($notification) {
            $params['notification'] = $notification;
        }
        if (!is_null($storeId)) {
            $params['store_id'] = $storeId;
        }
        return $this->_storeManager->getStore($storeId)->getUrl('magento_reward/customer/unsubscribe/', $params);
    }
}
