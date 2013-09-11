<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Checkout notification model
 *
 * @method \Magento\GoogleCheckout\Model\Resource\Notification _getResource()
 * @method \Magento\GoogleCheckout\Model\Resource\Notification getResource()
 * @method string getSerialNumber()
 * @method \Magento\GoogleCheckout\Model\Notification setSerialNumber(string $value)
 * @method string getStartedAt()
 * @method \Magento\GoogleCheckout\Model\Notification setStartedAt(string $value)
 * @method int getStatus()
 * @method \Magento\GoogleCheckout\Model\Notification setStatus(int $value)
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleCheckout\Model;

class Notification extends \Magento\Core\Model\AbstractModel
{
    const TIMEOUT_LIMIT = 3600;
    const STATUS_INPROCESS = 0;
    const STATUS_PROCESSED = 1;

    /**
     * Intialize model
     */
    function _construct()
    {
        $this->_init('Magento\GoogleCheckout\Model\Resource\Notification');
    }

    /**
     * Assign previously saved notification data to model
     *
     * @return \Magento\GoogleCheckout\Model\Notification
     */
    public function loadNotificationData()
    {
        $data = $this->getResource()->getNotificationData($this->getSerialNumber());
        if (is_array($data)) {
            $this->addData($data);
        }
        return $this;
    }

    /**
     * Check if current notification is already processed
     *
     * @return bool
     */
    public function isProcessed()
    {
        return $this->getStatus() == self::STATUS_PROCESSED;
    }

    /**
     * Check if current notification is time out
     *
     * @return bool
     */
    public function isTimeout()
    {
        $startedTime = strtotime($this->getStartedAt());
        $currentTime = time();

        if ($currentTime - $startedTime > self::TIMEOUT_LIMIT) {
            return true;
        }
        return false;
    }

    /**
     * Start process of current notification
     *
     * @return \Magento\GoogleCheckout\Model\Notification
     */
    public function startProcess()
    {
        $this->getResource()->startProcess($this->getSerialNumber());
        return $this;
    }

    /**
     * Update process of current notification
     *
     * @return \Magento\GoogleCheckout\Model\Notification
     */
    public function updateProcess()
    {
        $this->getResource()->updateProcess($this->getSerialNumber());
        return $this;
    }

    /**
     * Stop process of current notification
     *
     * @return \Magento\GoogleCheckout\Model\Notification
     */
    public function stopProcess()
    {
        $this->getResource()->stopProcess($this->getSerialNumber());
        return $this;
    }
}
