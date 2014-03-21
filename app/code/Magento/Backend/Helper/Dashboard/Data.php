<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Helper\Dashboard;

/**
 * Data helper for dashboard
 */
class Data extends \Magento\Core\Helper\Data
{
    /**
     * @var \Magento\Data\Collection\Db
     */
    protected $_stores;

    /**
     * @var string
     */
    protected $_installDate;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\State $appState
     * @param string $installDate
     * @param bool $dbCompatibleMode
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\App\State $appState,
        $installDate,
        $dbCompatibleMode = true
    ) {
        parent::__construct(
            $context,
            $coreStoreConfig,
            $storeManager,
            $appState,
            $dbCompatibleMode
        );
        $this->_installDate = $installDate;
    }

    /**
     * Retrieve stores configured in system.
     *
     * @return \Magento\Data\Collection\Db
     */
    public function getStores()
    {
        if (!$this->_stores) {
            $this->_stores = $this->_storeManager->getStore()->getResourceCollection()->load();
        }
        return $this->_stores;
    }

    /**
     * Retrieve number of loaded stores
     *
     * @return int
     */
    public function countStores()
    {
        return sizeof($this->_stores->getItems());
    }

    /**
     * Prepare array with periods for dashboard graphs
     *
     * @return array
     */
    public function getDatePeriods()
    {
        return array(
            '24h' => __('Last 24 Hours'),
            '7d'  => __('Last 7 Days'),
            '1m'  => __('Current Month'),
            '1y'  => __('YTD'),
            '2y'  => __('2YTD')
        );
    }

    /**
     * Create data hash to ensure that we got valid
     * data and it is not changed by some one else.
     *
     * @param string $data
     * @return string
     */
    public function getChartDataHash($data)
    {
        $secret = $this->_installDate;
        return md5($data . $secret);
    }
}
