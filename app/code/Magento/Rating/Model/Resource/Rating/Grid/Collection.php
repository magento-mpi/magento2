<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rating grid collection
 *
 * @category    Magento
 * @package     Magento_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rating_Model_Resource_Rating_Grid_Collection extends Magento_Rating_Model_Resource_Rating_Collection
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Collection constructor
     *
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     * @param array $data
     */
    public function __construct(
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Resource_Db_Abstract $resource = null,
        $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($fetchStrategy, $resource, $data);
    }

    /**
     * Add entity filter
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract|Magento_Rating_Model_Resource_Rating_Grid_Collection
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->addEntityFilter($this->_coreRegistry->registry('entityId'));
        return $this;
    }
}
