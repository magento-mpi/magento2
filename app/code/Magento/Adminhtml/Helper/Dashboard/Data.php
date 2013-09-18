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
     * Retrieve stores configured in system.
     *
     * @return array
     */
    public function getStores()
    {
        if(!$this->_stores) {
            $this->_stores = \Mage::app()->getStore()->getResourceCollection()->load();
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
