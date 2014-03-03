<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Resource\Entity;

use Magento\Core\Model\AbstractModel;
use Magento\Object;

/**
 * Eav Entity store resource model
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Store extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('eav_entity_store', 'entity_store_id');
    }

    /**
     * Load an object by entity type and store
     *
     * @param Object|AbstractModel $object
     * @param int $entityTypeId
     * @param int $storeId
     * @return bool
     */
    public function loadByEntityStore(AbstractModel $object, $entityTypeId, $storeId)
    {
        $adapter = $this->_getWriteAdapter();
        $bind    = array(
            ':entity_type_id' => $entityTypeId,
            ':store_id'       => $storeId
        );
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->forUpdate(true)
            ->where('entity_type_id = :entity_type_id')
            ->where('store_id = :store_id');
        $data = $adapter->fetchRow($select, $bind);

        if (!$data) {
            return false;
        }

        $object->setData($data);

        $this->_afterLoad($object);

        return true;
    }
}
