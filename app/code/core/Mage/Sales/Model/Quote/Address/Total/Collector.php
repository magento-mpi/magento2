<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Sales_Model_Quote_Address_Total_Collector
{
    protected $_models      = array();
    protected $_modelsConfig= array();
    protected $_collectors  = array();
    protected $_retrievers  = array();
    protected $_store;

    public function __construct($options)
    {
        if (isset($options['store'])) {
            $this->_store = $options['store'];
        } else {
            $this->_store = Mage::app()->getStore();
        }
        $this->_initModels()
            ->_initCollectors()
            ->_initRetrievers();
    }

    /**
     * Get total models array ordered for right calculation logic
     *
     * @return array
     */
    public function getCollectors()
    {
        return $this->_collectors;
    }

    /**
     * Get total models array ordered for right display sequence
     *
     * @return array
     */
    public function getRetrievers()
    {
        return $this->_retrievers;
    }

    /**
     * Initialize total models configuration and objects
     *
     * @return Mage_Sales_Model_Quote_Address_Total_Collector
     */
    protected function _initModels()
    {
        $totalsConfig = Mage::getConfig()->getNode('global/sales/quote/totals');

        foreach ($totalsConfig->children() as $totalCode=>$totalConfig) {
            $class = $totalConfig->getClassName();
            if ($class) {
                $model = Mage::getModel($class);
                if ($model instanceof Mage_Sales_Model_Quote_Address_Total_Abstract) {
                    $model->setCode($totalCode);
                    $this->_modelsConfig[$totalCode]= $this->_prepareConfigArray($totalCode, $totalConfig);
                    $this->_modelsConfig[$totalCode]= $model->processConfigArray(
                        $this->_modelsConfig[$totalCode],
                        $this->_store
                    );
                    $this->_models[$totalCode]      = $model;
                } else {
                    Mage::throwException(
                        Mage::helper('sales')->__('Address total model should be extended from Mage_Sales_Model_Quote_Address_Total_Abstract')
                    );
                }
            }
        }
        return $this;
    }

    /**
     * Prepare configuration array for total model
     *
     * @param   string $code
     * @param   Mage_Core_Model_Config_Element $totalConfig
     * @return  array
     */
    protected function _prepareConfigArray($code, $totalConfig)
    {
        $totalConfig = (array) $totalConfig;
        if (isset($totalConfig['before'])) {
            $totalConfig['before'] = explode(',',$totalConfig['before']);
        } else {
            $totalConfig['before'] = array();
        }
        if (isset($totalConfig['after'])) {
            $totalConfig['after'] = explode(',',$totalConfig['after']);
        } else {
            $totalConfig['after'] = array();
        }
        $totalConfig['_code'] = $code;
        return $totalConfig;
    }

    /**
     * Initialize collectors array.
     * Collectors array is array of total models ordered based on configuration settings
     *
     * @return  Mage_Sales_Model_Quote_Address_Total_Collector
     */
    protected function _initCollectors()
    {
        $configArray = $this->_modelsConfig;
        uasort($configArray, array($this, '_sortConfig'));
        foreach ($configArray as $code => $info) {
            $this->_collectors[$code] = $this->_models[$code];
        }
        return $this;
    }

    /**
     * uasort callback function
     *
     * @param   array $a
     * @param   array $b
     * @return  int
     */
    protected function _sortConfig($a, $b)
    {
        $aCode = $a['_code'];
        $bCode = $b['_code'];
        if (in_array($aCode, $b['before']) || in_array($bCode, $a['after'])) {
            return 1;
        } elseif (in_array($aCode, $b['after']) || in_array($bCode, $a['before'])) {
            return -1;
        }
        return 0;
    }

    /**
     * Initialize retrievers array
     *
     * @return Mage_Sales_Model_Quote_Address_Total_Collector
     */
    protected function _initRetrievers()
    {
        $sorts = Mage::getStoreConfig('sales/totals_sort', $this->_store);
        foreach ($sorts as $code => $sortOrder) {
            if (isset($this->_models[$code])) {
                $this->_retrievers[$sortOrder] = $this->_models[$code];
            }
        }
        ksort($this->_retrievers);
        $notSorted = array_diff(array_keys($this->_models), array_keys($sorts));
        foreach ($notSorted as $code) {
            $this->_retrievers[] = $this->_models[$code];
        }
        return $this;
    }
}