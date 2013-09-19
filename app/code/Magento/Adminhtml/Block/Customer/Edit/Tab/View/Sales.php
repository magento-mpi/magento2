<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customer view wishlist block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Customer\Edit\Tab\View;

class Sales extends \Magento\Backend\Block\Template
{

    /**
     * Sales entity collection
     *
     * @var \Magento\Sales\Model\Resource\Sale\Collection
     */
    protected $_collection;

    protected $_groupedCollection;
    protected $_websiteCounts;

    /**
     * Currency model
     *
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Registry $coreRegistry,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_storeManager = $storeManager;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_view_sales_grid');
    }

    public function _beforeToHtml()
    {
        $this->_currency = \Mage::getModel('Magento\Directory\Model\Currency')
            ->load($this->_storeConfig->getConfig(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE));

        $this->_collection = \Mage::getResourceModel('Magento\Sales\Model\Resource\Sale\Collection')
            ->setCustomerFilter($this->_coreRegistry->registry('current_customer'))
            ->setOrderStateFilter(\Magento\Sales\Model\Order::STATE_CANCELED, true)
            ->load();

        $this->_groupedCollection = array();

        foreach ($this->_collection as $sale) {
            if (!is_null($sale->getStoreId())) {
                $store      = \Mage::app()->getStore($sale->getStoreId());
                $websiteId  = $store->getWebsiteId();
                $groupId    = $store->getGroupId();
                $storeId    = $store->getId();

                $sale->setWebsiteId($store->getWebsiteId());
                $sale->setWebsiteName($store->getWebsite()->getName());
                $sale->setGroupId($store->getGroupId());
                $sale->setGroupName($store->getGroup()->getName());
            } else {
                $websiteId  = 0;
                $groupId    = 0;
                $storeId    = 0;

                $sale->setStoreName(__('Deleted Stores'));
            }

            $this->_groupedCollection[$websiteId][$groupId][$storeId] = $sale;
            $this->_websiteCounts[$websiteId] = isset($this->_websiteCounts[$websiteId])
                ? $this->_websiteCounts[$websiteId] + 1
                : 1;
        }

        return parent::_beforeToHtml();
    }

    public function getWebsiteCount($websiteId)
    {
        return isset($this->_websiteCounts[$websiteId]) ? $this->_websiteCounts[$websiteId] : 0;
    }

    public function getRows()
    {
        return $this->_groupedCollection;
    }

    public function getTotals()
    {
        return $this->_collection->getTotals();
    }

    /**
     * Format price by specified website
     *
     * @param float $price
     * @param null|int $websiteId
     * @return string
     */
    public function formatCurrency($price, $websiteId = null)
    {
        return \Mage::app()->getWebsite($websiteId)->getBaseCurrency()->format($price);
    }

    /**
     * Is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
