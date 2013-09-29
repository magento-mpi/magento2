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
 * EAV entity type resource model
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Resource\Entity;

class Type extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('eav_entity_type', 'entity_type_id');
    }

    /**
     * Load Entity Type by Code
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @param string $code
     * @return \Magento\Eav\Model\Resource\Entity\Type
     */
    public function loadByCode($object, $code)
    {
        return $this->load($object, $code, 'entity_type_code');
    }

    /**
     * Retrieve additional attribute table name for specified entity type
     *
     * @param integer $entityTypeId
     * @return string
     */
    public function getAdditionalAttributeTable($entityTypeId)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array('entity_type_id' => $entityTypeId);
        $select  = $adapter->select()
            ->from($this->getMainTable(), array('additional_attribute_table'))
            ->where('entity_type_id = :entity_type_id');

        return $adapter->fetchOne($select, $bind);
    }
}
