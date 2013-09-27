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
 */
class Magento_Adminhtml_Helper_Dashboard_Data extends Magento_Core_Helper_Data
{
    protected $_locale = null;
    protected $_stores = null;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Http $coreHttp
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Http $coreHttp,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($eventManager, $coreHttp, $context, $config, $coreStoreConfig);
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
        $secret = (string)$this->_config->getNode(Magento_Core_Model_Config_Primary::XML_PATH_INSTALL_DATE);
        return md5($data . $secret);
    }
}
