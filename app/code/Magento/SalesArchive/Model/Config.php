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
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Check archiving activity
     *
     * @return bool
     */
    public function isArchiveActive()
    {
        return $this->_scopeConfig->isSetFlag(
            self::XML_PATH_ARCHIVE_ACTIVE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve archive age
     *
     * @return int
     */
    public function getArchiveAge()
    {
        return (int)$this->_scopeConfig->getValue(
            self::XML_PATH_ARCHIVE_AGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve order statuses for archiving
     *
     * @return array|string[]
     */
    public function getArchiveOrderStatuses()
    {
        $statuses = $this->_scopeConfig->getValue(
            self::XML_PATH_ARCHIVE_ORDER_STATUSES,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (empty($statuses)) {
            return array();
        }

        return explode(',', $statuses);
    }
}
