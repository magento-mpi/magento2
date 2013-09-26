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
 * Customer account reward history block
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reward_Block_Customer_Reward_History extends Magento_Core_Block_Template
{
    /**
     * History records collection
     *
     * @var Magento_Reward_Model_Resource_Reward_History_Collection
     */
    protected $_collection = null;

    /**
     * Reward data
     *
     * @var Magento_Reward_Helper_Data
     */
    protected $_rewardData = null;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Reward_Model_Resource_Reward_History_CollectionFactory
     */
    protected $_historyFactory;

    /**
     * @param Magento_Reward_Helper_Data $rewardData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Reward_Model_Resource_Reward_History_CollectionFactory $historyFactory
     * @param array $data
     */
    public function __construct(
        Magento_Reward_Helper_Data $rewardData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Customer_Model_Session $customerSession,
        Magento_Reward_Model_Resource_Reward_History_CollectionFactory $historyFactory,
        array $data = array()
    ) {
        $this->_rewardData = $rewardData;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_historyFactory = $historyFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get history collection if needed
     *
     * @return Magento_Reward_Model_Resource_Reward_History_Collection|false
     */
    public function getHistory()
    {
        if (0 == $this->_getCollection()->getSize()) {
            return false;
        }
        return $this->_collection;
    }

    /**
     * History item points delta getter
     *
     * @param Magento_Reward_Model_Reward_History $item
     * @return string
     */
    public function getPointsDelta(Magento_Reward_Model_Reward_History $item)
    {
        return $this->_rewardData->formatPointsDelta($item->getPointsDelta());
    }

    /**
     * History item points balance getter
     *
     * @param Magento_Reward_Model_Reward_History $item
     * @return string
     */
    public function getPointsBalance(Magento_Reward_Model_Reward_History $item)
    {
        return $item->getPointsBalance();
    }

    /**
     * History item currency balance getter
     *
     * @param Magento_Reward_Model_Reward_History $item
     * @return string
     */
    public function getCurrencyBalance(Magento_Reward_Model_Reward_History $item)
    {
        return $this->_coreData->currency($item->getCurrencyAmount());
    }

    /**
     * History item reference message getter
     *
     * @param Magento_Reward_Model_Reward_History $item
     * @return string
     */
    public function getMessage(Magento_Reward_Model_Reward_History $item)
    {
        return $item->getMessage();
    }

    /**
     * History item reference additional explanation getter
     *
     * @param Magento_Reward_Model_Reward_History $item
     * @return string
     */
    public function getExplanation(Magento_Reward_Model_Reward_History $item)
    {
        return ''; // TODO
    }

    /**
     * History item creation date getter
     *
     * @param Magento_Reward_Model_Reward_History $item
     * @return string
     */
    public function getDate(Magento_Reward_Model_Reward_History $item)
    {
        return $this->_coreData->formatDate($item->getCreatedAt(), 'short', true);
    }

    /**
     * History item expiration date getter
     *
     * @param Magento_Reward_Model_Reward_History $item
     * @return string
     */
    public function getExpirationDate(Magento_Reward_Model_Reward_History $item)
    {
        $expiresAt = $item->getExpiresAt();
        if ($expiresAt) {
            return $this->_coreData->formatDate($expiresAt, 'short', true);
        }
        return '';
    }

    /**
     * Return reword points update history collection by customer and website
     *
     * @return Magento_Reward_Model_Resource_Reward_History_Collection
     */
    protected function _getCollection()
    {
        if (!$this->_collection) {
            $websiteId = $this->_storeManager->getWebsite()->getId();
            $this->_collection = $this->_historyFactory->create()
                ->addCustomerFilter($this->_customerSession->getCustomerId())
                ->addWebsiteFilter($websiteId)
                ->setExpiryConfig($this->_rewardData->getExpiryConfig())
                ->addExpirationDate($websiteId)
                ->skipExpiredDuplicates()
                ->setDefaultOrder()
            ;
        }
        return $this->_collection;
    }

    /**
     * Instantiate Pagination
     *
     * @return Magento_Reward_Block_Customer_Reward_History
     */
    protected function _prepareLayout()
    {
        if ($this->_isEnabled()) {
            $pager = $this->getLayout()->createBlock('Magento_Page_Block_Html_Pager', 'reward.history.pager')
                ->setCollection($this->_getCollection())->setIsOutputRequired(false)
            ;
            $this->setChild('pager', $pager);
        }
        return parent::_prepareLayout();
    }

    /**
     * Whether the history may show up
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_isEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Whether the history is supposed to be rendered
     *
     * @return bool
     */
    protected function _isEnabled()
    {
        return $this->_rewardData->isEnabledOnFront()
            && $this->_rewardData->getGeneralConfig('publish_history');
    }
}
