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
class Magento_Reward_Model_System_Config_Backend_Expiration extends Magento_Core_Model_Config_Value
{
    const XML_PATH_EXPIRATION_DAYS = 'magento_reward/general/expiration_days';

    /**
     * @var Magento_Reward_Model_Resource_Reward_HistoryFactory
     */
    protected $_historyFactory;

    /**
     * @var Magento_Core_Model_Resource_Config_Data_CollectionFactory
     */
    protected $_configCollFactory;

    /**
     * @param Magento_Core_Model_Resource_Config_Data_CollectionFactory $configCollFactory
     * @param Magento_Reward_Model_Resource_Reward_HistoryFactory $historyFactory
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Resource_Config_Data_CollectionFactory $configCollFactory,
        Magento_Reward_Model_Resource_Reward_HistoryFactory $historyFactory,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_historyFactory = $historyFactory;
        $this->_configCollFactory = $configCollFactory;
        parent::__construct(
            $context, $registry, $storeManager, $config, $resource, $resourceCollection, $data
        );
    }


    /**
     * Update history expiration date to simplify frontend calculations
     *
     * @return Magento_Reward_Model_System_Config_Backend_Expiration
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->isValueChanged()) {
            return $this;
        }

        $websiteIds = array();
        if ($this->getWebsiteCode()) {
            $websiteIds = array(Mage::app()->getWebsite($this->getWebsiteCode())->getId());
        } else {
            $collection = $this->_configCollFactory
                ->create()
                ->addFieldToFilter('path', self::XML_PATH_EXPIRATION_DAYS)
                ->addFieldToFilter('scope', 'websites');
            $websiteScopeIds = array();
            foreach ($collection as $item) {
                $websiteScopeIds[] = $item->getScopeId();
            }
            foreach (Mage::app()->getWebsites() as $website) {
                /* @var $website Magento_Core_Model_Website */
                if (!in_array($website->getId(), $websiteScopeIds)) {
                    $websiteIds[] = $website->getId();
                }
            }
        }
        if (count($websiteIds) > 0) {
            $this->_historyFactory
                ->create()
                ->updateExpirationDate($this->getValue(), $websiteIds);
        }

        return $this;
    }

    /**
     * The same as _beforeSave, but executed when website config extends default values
     *
     * @return Magento_Reward_Model_System_Config_Backend_Expiration
     */
    protected function _beforeDelete()
    {
        parent::_beforeDelete();
        if ($this->getWebsiteCode()) {
            $default = (string)$this->_coreConfig->getValue(self::XML_PATH_EXPIRATION_DAYS, 'default');
            $websiteIds = array(Mage::app()->getWebsite($this->getWebsiteCode())->getId());
            $this->_historyFactory
                ->create()
                ->updateExpirationDate($default, $websiteIds);
        }
        return $this;
    }
}
