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
 * Backend model for "Reward Points Lifetime"
 *
 */
namespace Magento\Reward\Model\System\Config\Backend;

class Expiration extends \Magento\Core\Model\Config\Value
{
    const XML_PATH_EXPIRATION_DAYS = 'magento_reward/general/expiration_days';

    /**
     * @var \Magento\Core\Model\Resource\Config\Data\CollectionFactory
     */
    protected $_configFactory;

    /**
     * @var \Magento\Reward\Model\Resource\Reward\HistoryFactory
     */
    protected $_historyFactory;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\Resource\Config\Data\CollectionFactory $configFactory
     * @param \Magento\Reward\Model\Resource\Reward\HistoryFactory $historyFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Config $config,
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
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Update history expiration date to simplify frontend calculations
     *
     * @return \Magento\Reward\Model\System\Config\Backend\Expiration
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->isValueChanged()) {
            return $this;
        }

        $websiteIds = array();
        if ($this->getWebsiteCode()) {
            $websiteIds = array($this->_storeManager->getWebsite($this->getWebsiteCode())->getId());
        } else {
            $collection = $this->_configFactory->create()
                ->addFieldToFilter('path', self::XML_PATH_EXPIRATION_DAYS)
                ->addFieldToFilter('scope', 'websites');
            $websiteScopeIds = array();
            foreach ($collection as $item) {
                $websiteScopeIds[] = $item->getScopeId();
            }
            foreach ($this->_storeManager->getWebsites() as $website) {
                /* @var $website \Magento\Core\Model\Website */
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
     * @return \Magento\Reward\Model\System\Config\Backend\Expiration
     */
    protected function _beforeDelete()
    {
        parent::_beforeDelete();
        if ($this->getWebsiteCode()) {
            $default = (string)$this->_config->getValue(self::XML_PATH_EXPIRATION_DAYS, 'default');
            $websiteIds = array($this->_storeManager->getWebsite($this->getWebsiteCode())->getId());
            $this->_historyFactory->create()->updateExpirationDate($default, $websiteIds);
        }
        return $this;
    }
}
