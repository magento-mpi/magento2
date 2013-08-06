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
 * @method Mage_Eav_Model_Resource_Entity_Store _getResource()
 * @method Mage_Eav_Model_Resource_Entity_Store getResource()
 * @method int getEntityTypeId()
 * @method Mage_Eav_Model_Entity_Store setEntityTypeId(int $value)
 * @method int getStoreId()
 * @method Mage_Eav_Model_Entity_Store setStoreId(int $value)
 * @method string getIncrementPrefix()
 * @method Mage_Eav_Model_Entity_Store setIncrementPrefix(string $value)
 * @method string getIncrementLastId()
 * @method Mage_Eav_Model_Entity_Store setIncrementLastId(string $value)
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Entity_Store extends Mage_Core_Model_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Eav_Model_Resource_Entity_Store');
    }

    /**
     * Load entity by store
     *
     * @param int $entityTypeId
     * @param int $storeId
     * @return Mage_Eav_Model_Entity_Store
     */
    public function loadByEntityStore($entityTypeId, $storeId)
    {
        $this->_getResource()->loadByEntityStore($this, $entityTypeId, $storeId);
        return $this;
    }
}
