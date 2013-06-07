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
     * @var Mage_Core_Model_Registry
     */
    protected $_registryManager;

    /**
     * @param Varien_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Mage_Core_Model_Registry $registryManager
     * @param Mage_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Varien_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Mage_Core_Model_Registry $registryManager,
        Mage_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        $this->_registryManager = $registryManager;
        parent::__construct($fetchStrategy, $resource);
    }

    /**
     *  Add filter by entity type id to collection
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract|Mage_Eav_Model_Resource_Entity_Attribute_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->setEntityTypeFilter($this->_registryManager->registry('entityType'));
        return $this;
    }
}
