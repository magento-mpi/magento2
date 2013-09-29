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
 * Data helper for dashboard
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Helper\Dashboard;

class Data extends \Magento\Core\Helper\Data
{
    protected $_locale = null;
    protected $_stores = null;

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @var string
     */
    protected $_installDate;

    /**
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Helper\Http $coreHttp
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Encryption $encryptor
     * @param string $installDate
     * @param bool $dbCompatibleMode      
     */
    public function __construct(
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Helper\Http $coreHttp,
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Encryption $encryptor,
        $installDate,
        $dbCompatibleMode = true
    ) {
        $this->_storeManager = $storeManager;
        $this->_installDate = $installDate;
        parent::__construct($eventManager, $coreHttp, $context, $config, $coreStoreConfig, 
            $encryptor, $dbCompatibleMode
        );
    }

    /**
     * Retrieve stores configured in system.
     *
     * @return array
     */
    public function getStores()
    {
        if(!$this->_stores) {
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
