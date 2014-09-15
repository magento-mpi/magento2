<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model\Resource;

/**
 * Gift Wrapping Resource Model
 *
 */
class Wrapping extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Wrapping websites table name
     *
     * @var string
     */
    protected $_websiteTable;

    /**
     * Wrapping stores data table name
     *
     * @var string
     */
    protected $_storeAttributesTable;

    /**
     * Intialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_giftwrapping', 'wrapping_id');
        $this->_websiteTable = $this->getTable('magento_giftwrapping_website');
        $this->_storeAttributesTable = $this->getTable('magento_giftwrapping_store_attributes');
    }

    /**
     * Add store data to wrapping data
     *
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            $this->_storeAttributesTable,
            array(
                'scope' => $adapter->getCheckSql('store_id = 0', $adapter->quote('default'), $adapter->quote('store')),
                'design'
            )
        )->where(
            'wrapping_id = ?',
            $object->getId()
        )->where(
            'store_id IN (0,?)',
            $object->getStoreId()
        );

        $data = $adapter->fetchAssoc($select);

        if (isset($data['store']) && is_array($data['store'])) {
            foreach ($data['store'] as $key => $value) {
                $object->setData($key, $value !== null ? $value : $data['default'][$key]);
                $object->setData($key . '_store', $value);
            }
        } else if (isset($data['default'])) {
            foreach ($data['default'] as $key => $value) {
                $object->setData($key, $value);
            }
        }
        return parent::_afterLoad($object);
    }

    /**
     * Get website ids associated to the gift wrapping
     *
     * @param  int $wrappingId
     * @return array
     */
    public function getWebsiteIds($wrappingId)
    {
        $select = $this->_getReadAdapter()->select()->from(
            $this->_websiteTable,
            'website_id'
        )->where(
            'wrapping_id = ?',
            $wrappingId
        );
        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Save wrapping per store view data
     *
     * @param  \Magento\GiftWrapping\Model\Wrapping $wrapping
     * @return void
     */
    public function saveWrappingStoreData($wrapping)
    {
        $initialDesign = $wrapping->getDesign();
        //this check to prevent saving default data from store view
        if ($wrapping->hasData('is_default') && is_array($wrapping->getData('is_default'))) {
            foreach ($wrapping->getData('is_default') as $key => $value) {
                if ($value) {
                    $wrapping->setData($key, null);
                }
            }
        }

        if (!is_null($initialDesign)) {
            $this->_getWriteAdapter()->delete(
                $this->_storeAttributesTable,
                array('wrapping_id = ?' => $wrapping->getId(), 'store_id = ?' => $wrapping->getStoreId())
            );

            if ($wrapping->getDesign()) {
                $this->_getWriteAdapter()->insert(
                    $this->_storeAttributesTable,
                    array(
                        'wrapping_id' => $wrapping->getId(),
                        'store_id' => $wrapping->getStoreId(),
                        'design' => $wrapping->getDesign()
                    )
                );
            }
        }
    }

    /**
     * Save attached websites
     *
     * @param  \Magento\GiftWrapping\Model\Wrapping $wrapping
     * @return void
     */
    public function saveWrappingWebsiteData($wrapping)
    {
        $websiteIds = $wrapping->getWebsiteIds();
        $this->_getWriteAdapter()->delete($this->_websiteTable, array('wrapping_id = ?' => $wrapping->getId()));

        foreach ($websiteIds as $value) {
            $this->_getWriteAdapter()->insert(
                $this->_websiteTable,
                array('wrapping_id' => $wrapping->getId(), 'website_id' => $value)
            );
        }
    }

    /**
     * Update gift wrapping status
     *
     * @param int $status new status can be 1 or 0
     * @param array $wrappingIds target wrapping IDs
     * @return void
     */
    public function updateStatus($status, array $wrappingIds)
    {
        $this->_getWriteAdapter()->update(
            $this->getMainTable(),
            array('status' => (int)(bool)$status),
            array('wrapping_id IN(?)' => $wrappingIds)
        );
    }
}
