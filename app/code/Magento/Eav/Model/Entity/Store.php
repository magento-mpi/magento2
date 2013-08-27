<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @method Magento_Eav_Model_Resource_Entity_Store _getResource()
 * @method Magento_Eav_Model_Resource_Entity_Store getResource()
 * @method int getEntityTypeId()
 * @method Magento_Eav_Model_Entity_Store setEntityTypeId(int $value)
 * @method int getStoreId()
 * @method Magento_Eav_Model_Entity_Store setStoreId(int $value)
 * @method string getIncrementPrefix()
 * @method Magento_Eav_Model_Entity_Store setIncrementPrefix(string $value)
 * @method string getIncrementLastId()
 * @method Magento_Eav_Model_Entity_Store setIncrementLastId(string $value)
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Entity_Store extends Magento_Core_Model_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Eav_Model_Resource_Entity_Store');
    }

    /**
     * Load entity by store
     *
     * @param int $entityTypeId
     * @param int $storeId
     * @return Magento_Eav_Model_Entity_Store
     */
    public function loadByEntityStore($entityTypeId, $storeId)
    {
        $this->_getResource()->loadByEntityStore($this, $entityTypeId, $storeId);
        return $this;
    }
}
