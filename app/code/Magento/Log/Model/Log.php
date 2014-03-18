<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\Model;

/**
 * Log Model
 *
 * @method \Magento\Log\Model\Resource\Log _getResource()
 * @method \Magento\Log\Model\Resource\Log getResource()
 * @method string getSessionId()
 * @method \Magento\Log\Model\Log setSessionId(string $value)
 * @method string getFirstVisitAt()
 * @method \Magento\Log\Model\Log setFirstVisitAt(string $value)
 * @method string getLastVisitAt()
 * @method \Magento\Log\Model\Log setLastVisitAt(string $value)
 * @method int getLastUrlId()
 * @method \Magento\Log\Model\Log setLastUrlId(int $value)
 * @method int getStoreId()
 * @method \Magento\Log\Model\Log setStoreId(int $value)
 *
 * @category    Magento
 * @package     Magento_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Log extends \Magento\Core\Model\AbstractModel
{
    const XML_LOG_CLEAN_DAYS    = 'system/log/clean_after_day';

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeConfig = $coreStoreConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init Resource Model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Log\Model\Resource\Log');
    }

    /**
     * Return log clean time in seconds
     *
     * @return null|string
     */
    public function getLogCleanTime()
    {
        return $this->_storeConfig->getValue(self::XML_LOG_CLEAN_DAYS, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE) * 60 * 60 * 24;
    }

    /**
     * Clean Logs
     *
     * @return $this
     */
    public function clean()
    {
        $this->getResource()->clean($this);
        return $this;
    }
}
