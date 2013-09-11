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
 * @method \Magento\Eav\Model\Resource\Entity\Store _getResource()
 * @method \Magento\Eav\Model\Resource\Entity\Store getResource()
 * @method int getEntityTypeId()
 * @method \Magento\Eav\Model\Entity\Store setEntityTypeId(int $value)
 * @method int getStoreId()
 * @method \Magento\Eav\Model\Entity\Store setStoreId(int $value)
 * @method string getIncrementPrefix()
 * @method \Magento\Eav\Model\Entity\Store setIncrementPrefix(string $value)
 * @method string getIncrementLastId()
 * @method \Magento\Eav\Model\Entity\Store setIncrementLastId(string $value)
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Entity;

class Store extends \Magento\Core\Model\AbstractModel
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Magento\Eav\Model\Resource\Entity\Store');
    }

    /**
     * Load entity by store
     *
     * @param int $entityTypeId
     * @param int $storeId
     * @return \Magento\Eav\Model\Entity\Store
     */
    public function loadByEntityStore($entityTypeId, $storeId)
    {
        $this->_getResource()->loadByEntityStore($this, $entityTypeId, $storeId);
        return $this;
    }
}
