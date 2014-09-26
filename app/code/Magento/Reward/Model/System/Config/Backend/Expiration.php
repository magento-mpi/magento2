<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\System\Config\Backend;

/**
 * Backend model for "Reward Points Lifetime"
 */
class Expiration extends \Magento\Framework\App\Config\Value
{
    const XML_PATH_EXPIRATION_DAYS = 'magento_reward/general/expiration_days';

    /**
     * Core config collection
     * @var \Magento\Core\Model\Resource\Config\Data\CollectionFactory
     */
    protected $_configFactory;

    /**
     * Reward history factory
     *
     * @var \Magento\Reward\Model\Resource\Reward\HistoryFactory
     */
    protected $_historyFactory;

    /** @var \Magento\Framework\StoreManagerInterface */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Core\Model\Resource\Config\Data\CollectionFactory $configFactory
     * @param \Magento\Reward\Model\Resource\Reward\HistoryFactory $historyFactory
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Resource\Config\Data\CollectionFactory $configFactory,
        \Magento\Reward\Model\Resource\Reward\HistoryFactory $historyFactory,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_config = $config;
        $this->_configFactory = $configFactory;
        $this->_historyFactory = $historyFactory;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Update history expiration date to simplify frontend calculations
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->isValueChanged()) {
            return $this;
        }

        $websiteIds = array();
        if ($this->getScope() == \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES) {
            $websiteIds = array($this->_storeManager->getWebsite($this->getScopeCode())->getId());
        } else {
            $collection = $this->_configFactory->create()->addFieldToFilter(
                'path',
                self::XML_PATH_EXPIRATION_DAYS
            )->addFieldToFilter(
                'scope',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES
            );
            $websiteScopeIds = array();
            foreach ($collection as $item) {
                $websiteScopeIds[] = $item->getScopeId();
            }
            foreach ($this->_storeManager->getWebsites() as $website) {
                /* @var $website \Magento\Store\Model\Website */
                if (!in_array($website->getId(), $websiteScopeIds)) {
                    $websiteIds[] = $website->getId();
                }
            }
        }
        if (count($websiteIds) > 0) {
            $this->_historyFactory->create()->updateExpirationDate($this->getValue(), $websiteIds);
        }

        return $this;
    }

    /**
     * The same as _beforeSave, but executed when website config extends default values
     *
     * @return $this
     */
    protected function _beforeDelete()
    {
        parent::_beforeDelete();
        if ($this->getScope() == \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES) {
            $default = (string)$this->_config->getValue(self::XML_PATH_EXPIRATION_DAYS, 'default');
            $websiteIds = array($this->_storeManager->getWebsite($this->getScopeCode())->getId());
            $this->_historyFactory->create()->updateExpirationDate($default, $websiteIds);
        }
        return $this;
    }
}
