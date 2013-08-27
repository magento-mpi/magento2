<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer account reward history block
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Customer_Reward_History extends Magento_Core_Block_Template
{
    /**
     * History records collection
     *
     * @var Enterprise_Reward_Model_Resource_Reward_History_Collection
     */
    protected $_collection = null;

    /**
     * Reward data
     *
     * @var Enterprise_Reward_Helper_Data
     */
    protected $_rewardData = null;

    /**
     * @param Enterprise_Reward_Helper_Data $rewardData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_Reward_Helper_Data $rewardData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_rewardData = $rewardData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get history collection if needed
     *
     * @return Enterprise_Reward_Model_Resource_Reward_History_Collection|false
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
     * @param Enterprise_Reward_Model_Reward_History $item
     * @return string
     */
    public function getPointsDelta(Enterprise_Reward_Model_Reward_History $item)
    {
        return $this->_rewardData->formatPointsDelta($item->getPointsDelta());
    }

    /**
     * History item points balance getter
     *
     * @param Enterprise_Reward_Model_Reward_History $item
     * @return string
     */
    public function getPointsBalance(Enterprise_Reward_Model_Reward_History $item)
    {
        return $item->getPointsBalance();
    }

    /**
     * History item currency balance getter
     *
     * @param Enterprise_Reward_Model_Reward_History $item
     * @return string
     */
    public function getCurrencyBalance(Enterprise_Reward_Model_Reward_History $item)
    {
        return $this->_coreData->currency($item->getCurrencyAmount());
    }

    /**
     * History item reference message getter
     *
     * @param Enterprise_Reward_Model_Reward_History $item
     * @return string
     */
    public function getMessage(Enterprise_Reward_Model_Reward_History $item)
    {
        return $item->getMessage();
    }

    /**
     * History item reference additional explanation getter
     *
     * @param Enterprise_Reward_Model_Reward_History $item
     * @return string
     */
    public function getExplanation(Enterprise_Reward_Model_Reward_History $item)
    {
        return ''; // TODO
    }

    /**
     * History item creation date getter
     *
     * @param Enterprise_Reward_Model_Reward_History $item
     * @return string
     */
    public function getDate(Enterprise_Reward_Model_Reward_History $item)
    {
        return $this->_coreData->formatDate($item->getCreatedAt(), 'short', true);
    }

    /**
     * History item expiration date getter
     *
     * @param Enterprise_Reward_Model_Reward_History $item
     * @return string
     */
    public function getExpirationDate(Enterprise_Reward_Model_Reward_History $item)
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
     * @return Enterprise_Reward_Model_Resource_Reward_History_Collection
     */
    protected function _getCollection()
    {
        if (!$this->_collection) {
            $websiteId = Mage::app()->getWebsite()->getId();
            $this->_collection = Mage::getModel('Enterprise_Reward_Model_Reward_History')->getCollection()
                ->addCustomerFilter(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId())
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
     * @return Enterprise_Reward_Block_Customer_Reward_History
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
