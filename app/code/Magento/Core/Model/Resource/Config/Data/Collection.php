<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Config data collection
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Resource_Config_Data_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Core_Model_Config_Data', 'Magento_Core_Model_Resource_Config_Data');
    }

    /**
     * Add scope filter to collection
     *
     * @param string $scope
     * @param int $scopeId
     * @param string $section
     * @return Magento_Core_Model_Resource_Config_Data_Collection
     */
    public function addScopeFilter($scope, $scopeId, $section)
    {
        $this->addFieldToFilter('scope', $scope);
        $this->addFieldToFilter('scope_id', $scopeId);
        $this->addFieldToFilter('path', array('like' => $section . '/%'));
        return $this;
    }

    /**
     *  Add path filter
     *
     * @param string $section
     * @return Magento_Core_Model_Resource_Config_Data_Collection
     */
    public function addPathFilter($section)
    {
        $this->addFieldToFilter('path', array('like' => $section . '/%'));
        return $this;
    }

    /**
     * Add value filter
     *
     * @param int|string $value
     * @return Magento_Core_Model_Resource_Config_Data_Collection
     */
    public function addValueFilter($value)
    {
        $this->addFieldToFilter('value', array('like' => $value));
        return $this;
    }
}
