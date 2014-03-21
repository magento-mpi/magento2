<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Model;

/**
 * Order archive config model
 *
 */
class Config
{
    const XML_PATH_ARCHIVE_ACTIVE = 'sales/magento_salesarchive/active';
    const XML_PATH_ARCHIVE_AGE = 'sales/magento_salesarchive/age';
    const XML_PATH_ARCHIVE_ORDER_STATUSES = 'sales/magento_salesarchive/order_statuses';

    /**
     * Core store config
     *
     * @var \Magento\Store\Model\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\Store\Model\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\Store\Model\Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Check archiving activity
     *
     * @return bool
     */
    public function isArchiveActive()
    {
        return $this->_coreStoreConfig->isSetFlag(self::XML_PATH_ARCHIVE_ACTIVE, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
    }

    /**
     * Retrieve archive age
     *
     * @return int
     */
    public function getArchiveAge()
    {
        return (int) $this->_coreStoreConfig->getValue(self::XML_PATH_ARCHIVE_AGE, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
    }

    /**
     * Retrieve order statuses for archiving
     *
     * @return array|string[]
     */
    public function getArchiveOrderStatuses()
    {
        $statuses = $this->_coreStoreConfig->getValue(self::XML_PATH_ARCHIVE_ORDER_STATUSES, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE);

        if (empty($statuses)) {
            return array();
        }

        return explode(',', $statuses);
    }
}
