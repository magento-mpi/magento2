<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Eav\Model\Resource;

/**
 * Eav Resource Config model
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Config extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Array of entity types
     *
     * @var array
     */
    protected static $_entityTypes   = array();

    /**
     * Array of attributes
     *
     * @var array
     */
    protected static $_attributes    = array();

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('eav_entity_type', 'entity_type_id');
    }

    /**
     * Load all entity types
     *
     * @return $this
     */
    protected function _loadTypes()
    {
        $adapter = $this->_getReadAdapter();
        if (!$adapter) {
            return $this;
        }
        if (empty(self::$_entityTypes)) {
            $select = $adapter->select()->from($this->getMainTable());
            $data   = $adapter->fetchAll($select);
            foreach ($data as $row) {
                self::$_entityTypes['by_id'][$row['entity_type_id']] = $row;
                self::$_entityTypes['by_code'][$row['entity_type_code']] = $row;
            }
        }

        return $this;
    }

    /**
     * Load attribute types
     *
     * @param int $typeId
     * @return array
     */
    protected function _loadTypeAttributes($typeId)
    {
        if (!isset(self::$_attributes[$typeId])) {
            $adapter = $this->_getReadAdapter();
            $bind    = array('entity_type_id' => $typeId);
            $select  = $adapter->select()
                ->from($this->getTable('eav_attribute'))
                ->where('entity_type_id = :entity_type_id');

            self::$_attributes[$typeId] = $adapter->fetchAll($select, $bind);
        }

        return self::$_attributes[$typeId];
    }

    /**
     * Retrieve entity type data
     *
     * @param string $entityType
     * @return array
     */
    public function fetchEntityTypeData($entityType)
    {
        $this->_loadTypes();

        if (is_numeric($entityType)) {
            $info = isset(self::$_entityTypes['by_id'][$entityType])
                ? self::$_entityTypes['by_id'][$entityType] : null;
        } else {
            $info = isset(self::$_entityTypes['by_code'][$entityType])
                ? self::$_entityTypes['by_code'][$entityType] : null;
        }

        $data = array();
        if ($info) {
            $data['entity']     = $info;
            $attributes         = $this->_loadTypeAttributes($info['entity_type_id']);
            $data['attributes'] = array();
            foreach ($attributes as $attribute) {
                $data['attributes'][$attribute['attribute_id']] = $attribute;
                $data['attributes'][$attribute['attribute_code']] = $attribute['attribute_id'];
            }
        }

        return $data;
    }
}
