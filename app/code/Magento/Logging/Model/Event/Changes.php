<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Model\Event;

/**
 * Logging event changes model
 *
 * @method \Magento\Logging\Model\Resource\Event\Changes _getResource()
 * @method \Magento\Logging\Model\Resource\Event\Changes getResource()
 * @method string getSourceName()
 * @method \Magento\Logging\Model\Event\Changes setSourceName(string $value)
 * @method int getEventId()
 * @method \Magento\Logging\Model\Event\Changes setEventId(int $value)
 * @method int getSourceId()
 * @method \Magento\Logging\Model\Event\Changes setSourceId(int $value)
 * @method string getOriginalData()
 * @method \Magento\Logging\Model\Event\Changes setOriginalData(string $value)
 * @method string getResultData()
 * @method \Magento\Logging\Model\Event\Changes setResultData(string $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Changes extends \Magento\Framework\Model\AbstractModel
{
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
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $skipFields
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $skipFields = array(),
        array $data = array()
    ) {
        $this->_globalSkipFields = $skipFields;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource
     * Get fields that should not be logged for all models
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Logging\Model\Resource\Event\Changes');
    }

    /**
     * Set some data automatically before saving model
     *
     * @return \Magento\Logging\Model\Event
     */
    public function beforeSave()
    {
        $this->_calculateDifference();
        $this->setOriginalData(serialize($this->getOriginalData()));
        $this->setResultData(serialize($this->getResultData()));
        return parent::beforeSave();
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
            } elseif ($origData && !$resultData) {
                $newResultData = array('__was_deleted' => true);
                $difference = $origData;
            } elseif ($origData && $resultData) {
                $newParams = array_diff_key($resultData, $origData);
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
     * @return void
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
            if (!in_array(
                $key,
                $this->_globalSkipFields
            ) && !in_array(
                $key,
                $skipFields
            ) && !is_array(
                $value
            ) && !is_object(
                $value
            )
            ) {
                $clearedData[$key] = $value;
            }
        }
        return $clearedData;
    }
}
