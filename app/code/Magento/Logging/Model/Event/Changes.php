<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Logging event changes model
 *
 * @method Magento_Logging_Model_Resource_Event_Changes _getResource()
 * @method Magento_Logging_Model_Resource_Event_Changes getResource()
 * @method string getSourceName()
 * @method Magento_Logging_Model_Event_Changes setSourceName(string $value)
 * @method int getEventId()
 * @method Magento_Logging_Model_Event_Changes setEventId(int $value)
 * @method int getSourceId()
 * @method Magento_Logging_Model_Event_Changes setSourceId(int $value)
 * @method string getOriginalData()
 * @method Magento_Logging_Model_Event_Changes setOriginalData(string $value)
 * @method string getResultData()
 * @method Magento_Logging_Model_Event_Changes setResultData(string $value)
 *
 * @category    Magento
 * @package     Magento_Logging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Logging_Model_Event_Changes extends Magento_Core_Model_Abstract
{
    /**
     * Config path to fields that must be not be logged for all models
     *
     */
    const XML_PATH_SKIP_GLOBAL_FIELDS = 'adminhtml/magento/logging/skip_fields';

    /**
     * Set of fields that should not be logged for all models
     *
     * @var array
     */
    protected $_globalSkipFields = array();

    /**
     * Set of fields that should not be logged per expected model
     *
     * @var array
     */
    protected $_skipFields = array();

    /**
     * Store difference between original data and result data of model
     *
     * @var array
     */
    protected $_difference = null;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Config $coreConfig,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Initialize resource
     * Get fields that should not be logged for all models
     *
     */
    protected function _construct()
    {
        $this->_globalSkipFields = array_map('trim', array_filter(explode(',',
            (string)$this->_coreConfig->getNode(self::XML_PATH_SKIP_GLOBAL_FIELDS))));

        $this->_init('Magento_Logging_Model_Resource_Event_Changes');
    }

    /**
     * Set some data automatically before saving model
     *
     * @return Magento_Logging_Model_Event
     */
    protected function _beforeSave()
    {
        $this->_calculateDifference();
        $this->setOriginalData(serialize($this->getOriginalData()));
        $this->setResultData(serialize($this->getResultData()));
        return parent::_beforeSave();
    }

    /**
     * Define if current model has difference between original and result data
     *
     * @return bool
     */
    public function hasDifference()
    {
        $difference = $this->_calculateDifference();
        return !empty($difference);
    }

    /**
     * Calculate difference between original and result data and return that difference
     *
     * @return null|array|int
     */
    protected function _calculateDifference()
    {
        if (is_null($this->_difference)) {
            $updatedParams = $newParams = $sameParams = $difference = array();
            $newOriginalData = $origData = $this->getOriginalData();
            $newResultData = $resultData = $this->getResultData();

            if (!is_array($origData)) {
                $origData = array();
            }
            if (!is_array($resultData)) {
                $resultData = array();
            }

            if (!$origData && $resultData) {
                $newOriginalData = array('__was_created' => true);
                $difference = $resultData;
            }
            elseif ($origData && !$resultData) {
                $newResultData = array('__was_deleted' => true);
                $difference = $origData;
            }
            elseif ($origData && $resultData) {
                $newParams  = array_diff_key($resultData, $origData);
                $sameParams = array_intersect_key($origData, $resultData);
                foreach ($sameParams as $key => $value) {
                    if ($origData[$key] != $resultData[$key]) {
                        $updatedParams[$key] = $resultData[$key];
                    }
                }
                $newOriginalData = array_intersect_key($origData, $updatedParams);
                $difference = $newResultData = array_merge($updatedParams, $newParams);
                if ($difference && !$updatedParams) {
                    $newOriginalData = array('__no_changes' => true);
                }
            }

            $this->setOriginalData($newOriginalData);
            $this->setResultData($newResultData);

            $this->_difference = $difference;
        }
        return $this->_difference;
    }

    /**
     * Set skip fields and clear model data
     *
     * @param array $skipFields
     */
    public function cleanupData($skipFields)
    {
        if ($skipFields && is_array($skipFields)) {
            $this->_skipFields = $skipFields;
        }
        $this->setOriginalData($this->_cleanupData($this->getOriginalData()));
        $this->setResultData($this->_cleanupData($this->getResultData()));
    }

    /**
     * Clear model data from objects, arrays and fields that should be skipped
     *
     * @param array $data
     * @return array
     */
    protected function _cleanupData($data)
    {
        if (!$data || !is_array($data)) {
            return array();
        }
        $skipFields = $this->_skipFields;
        if (!$skipFields || !is_array($skipFields)) {
            $skipFields = array();
        }
        $clearedData = array();
        foreach ($data as $key => $value) {
            if (!in_array($key, $this->_globalSkipFields) && !in_array($key, $skipFields) && !is_array($value) && !is_object($value)) {
                $clearedData[$key] = $value;
            }
        }
        return $clearedData;
    }
}
