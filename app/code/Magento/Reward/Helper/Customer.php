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

use Magento\Core\Model\Store;

class Customer extends \Magento\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Return Unsubscribe notification URL
     *
     * @param string|bool $notification Notification type
     * @param int|string|Store $storeId
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
