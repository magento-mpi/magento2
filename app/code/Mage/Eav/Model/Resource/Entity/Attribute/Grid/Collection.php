<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Eav Resource Attribute Set Collection
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Entity_Attribute_Grid_Collection
    extends Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection
{
    /**
     * @var Magento_Core_Model_Registry
     */
    protected $_registryManager;

    /**
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_Registry $registryManager
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_Registry $registryManager,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        $this->_registryManager = $registryManager;
        parent::__construct($fetchStrategy, $resource);
    }

    /**
     *  Add filter by entity type id to collection
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract|Mage_Eav_Model_Resource_Entity_Attribute_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->setEntityTypeFilter($this->_registryManager->registry('entityType'));
        return $this;
    }
}
