<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\System\Config\Backend;

/**
 * Backend model for "Reward Points Lifetime"
 */
class Expiration extends \Magento\Core\Model\Config\Value
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

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\Config\ScopeConfigInterface $config
     * @param \Magento\Core\Model\Resource\Config\Data\CollectionFactory $configFactory
     * @param \Magento\Reward\Model\Resource\Reward\HistoryFactory $historyFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\App\Config\ScopeConfigInterface $config,
        \Magento\Core\Model\Resource\Config\Data\CollectionFactory $configFactory,
        \Magento\Reward\Model\Resource\Reward\HistoryFactory $historyFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
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
            $collection = $this->_configFactory->create()
                ->addFieldToFilter('path', self::XML_PATH_EXPIRATION_DAYS)
                ->addFieldToFilter('scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES);
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
