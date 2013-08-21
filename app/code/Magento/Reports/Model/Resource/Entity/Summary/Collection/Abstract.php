<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reports summary collection
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Model_Resource_Entity_Summary_Collection_Abstract extends Magento_Data_Collection
{
    /**
     * Entity collection for summaries
     *
     * @var Magento_Eav_Model_Entity_Collection_Abstract
     */
    protected $_entityCollection;

    /**
     * Filters the summaries by some period
     *
     * @param string $periodType
     * @param string|int|null $customStart
     * @param string|int|null $customEnd
     * @return Magento_Reports_Model_Resource_Entity_Summary_Collection_Abstract
     */
    public function setSelectPeriod($periodType, $customStart = null, $customEnd = null)
    {
        switch ($periodType) {
            case "24h":
                $customStart = Magento_Date::toTimestamp(true) - 86400;
                $customEnd   = Magento_Date::toTimestamp(true);
                break;

            case "7d":
                $customStart = Magento_Date::toTimestamp(true) - 604800;
                $customEnd   = Magento_Date::toTimestamp(true);
                break;

            case "30d":
                $customStart = Magento_Date::toTimestamp(true) - 2592000;
                $customEnd   = Magento_Date::toTimestamp(true);
                break;

            case "1y":
                $customStart = Magento_Date::toTimestamp(true) - 31536000;
                $customEnd   = Magento_Date::toTimestamp(true);
                break;

            default:
                if (is_string($customStart)) {
                    $customStart = strtotime($customStart);
                }
                if (is_string($customEnd)) {
                    $customEnd = strtotime($customEnd);
                }
                break;

        }


        return $this;
    }

    /**
     * Set date period
     *
     * @param int $period
     * @return Magento_Reports_Model_Resource_Entity_Summary_Collection_Abstract
     */
    public function setDatePeriod($period)
    {
        return $this;
    }

    /**
     * Set store filter
     *
     * @param int $storeId
     * @return Magento_Reports_Model_Resource_Entity_Summary_Collection_Abstract
     */
    public function setStoreFilter($storeId)
    {
        return $this;
    }

    /**
     * Return collection for summaries
     *
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    public function getCollection()
    {
        if (empty($this->_entityCollection)) {
            $this->_initCollection();
        }
        return $this->_entityCollection;
    }

    /**
     * Init collection
     *
     * @return Magento_Reports_Model_Resource_Entity_Summary_Collection_Abstract
     */
    protected function _initCollection()
    {
        return $this;
    }
}
