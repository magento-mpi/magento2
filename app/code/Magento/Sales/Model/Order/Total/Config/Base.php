<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration class for totals
 *
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Order_Total_Config_Base extends Magento_Sales_Model_Config_Ordered
{
    /**
     * Cache key for collectors
     *
     * @var string
     */
    protected $_collectorsCacheKey = 'sorted_collectors';

    /**
     * Total models list
     *
     * @var array
     */
    protected $_totalModels = array();

    /**
     * Configuration path where to collect registered totals
     *
     * @var string
     */
    protected $_totalsConfigNode = 'totals';

    /**
     * Init model class by configuration
     *
     * @param string $class
     * @param string $totalCode
     * @param array $totalConfig
     * @return Magento_Sales_Model_Order_Total_Abstract
     */
    protected function _initModelInstance($class, $totalCode, $totalConfig)
    {
        $model = Mage::getModel($class);
        if (!$model instanceof Magento_Sales_Model_Order_Total_Abstract) {
            Mage::throwException(__('The total model should be extended from Magento_Sales_Model_Order_Total_Abstract.'));
        }

        $model->setCode($totalCode);
        $model->setTotalConfigNode($totalConfig);
        $this->_modelsConfig[$totalCode] = $this->_prepareConfigArray($totalCode, $totalConfig);
        $this->_modelsConfig[$totalCode] = $model->processConfigArray($this->_modelsConfig[$totalCode]);
        return $model;
    }

    /**
     * Retrieve total calculation models
     *
     * @return array
     */
    public function getTotalModels()
    {
        if (empty($this->_totalModels)) {
            $this->_initModels();
            $this->_initCollectors();
            $this->_totalModels = $this->_collectors;
        }
        return $this->_totalModels;
    }
}
