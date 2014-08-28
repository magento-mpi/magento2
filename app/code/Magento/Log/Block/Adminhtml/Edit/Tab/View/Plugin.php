<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Log\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Block\Adminhtml\Edit\Tab\View;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Plugin
 * @package Magento\Log\Block\Adminhtml\Edit\Tab\View
 */
class Plugin
{
    /**
     * @var \Magento\Log\Model\Customer
     */
    protected $customerLog;

    /**
     * @var \Magento\Log\Model\Visitor
     */
    protected $modelLog;

    /**
     * @var \Magento\Log\Model\CustomerFactory
     */
    protected $logFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Log\Model\CustomerFactory $logFactory
     * @param \Magento\Log\Model\Log $modelLog
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Log\Model\CustomerFactory $logFactory,
        \Magento\Log\Model\Log $modelLog,
        \Magento\Framework\Stdlib\DateTime $dateTime
    ) {
        $this->logFactory = $logFactory;
        $this->modelLog = $modelLog;
        $this->localeDate = $context->getLocaleDate();
        $this->dateTime = $dateTime;
    }

    /**
     * Get customer's current status
     *
     * @param View $object
     * @return string
     */
    public function afterGetCurrentStatus($object)
    {
        $log = $this->getCustomerLog($object);
        $interval = $this->modelLog->getOnlineMinutesInterval();
        if ($log->getLogoutAt() ||
            strtotime($this->dateTime->now()) - strtotime($log->getLastVisitAt()) > $interval * 60
        ) {
            return __('Offline');
        }
        return __('Online');
    }

    /**
     * Get customer last login date
     *
     * @param View $object
     * @return string
     */
    public function afterGetLastLoginDate($object)
    {
        $date = $this->getCustomerLog($object)->getLoginAtTimestamp();
        if ($date) {
            return $object->formatDate($date, TimezoneInterface::FORMAT_TYPE_MEDIUM, true);
        }
        return __('Never');
    }

    /**
     * @param View $object
     * @return string
     */
    public function afterGetStoreLastLoginDate($object)
    {
        $date = $this->getCustomerLog($object)->getLoginAtTimestamp();
        if ($date) {
            $date = $this->localeDate->scopeDate($object->getCustomer()->getStoreId(), $date, true);
            return $object->formatDate($date, TimezoneInterface::FORMAT_TYPE_MEDIUM, true);
        }
        return __('Never');
    }

    /**
     * Load Customer Log model
     *
     * @param View $object
     * @return \Magento\Log\Model\Customer
     */
    public function getCustomerLog($object)
    {
        if (!$this->customerLog) {
            $this->customerLog = $this->logFactory->create()->loadByCustomer($object->getCustomerId());
        }
        return $this->customerLog;
    }
}
