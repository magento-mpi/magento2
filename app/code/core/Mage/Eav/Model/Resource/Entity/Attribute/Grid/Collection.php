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
     * @param Mage_Core_Model_Resource_Db_Abstract $resource
     * @param Mage_Core_Model_Registry $registry
     */
    public function __construct(
        Mage_Core_Model_Registry $registry, Mage_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        parent::__construct($resource);
        $this->_registryManager = $registry;
    }

    /**
     * Prepare select for load
     *
     * @param Varien_Db_select $select
     * @return string
     */
    protected function _prepareSelect(Varien_Db_Select $select)
    {
        $this->setEntityTypeFilter($this->_registryManager->registry('entityType'));
        return parent::_prepareSelect($select);
    }
}
