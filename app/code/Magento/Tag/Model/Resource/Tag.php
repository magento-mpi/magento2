<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tag resourse model
 *
 * @category    Magento
 * @package     Magento_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Model_Resource_Tag extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Core string
     *
     * @var Magento_Core_Helper_String
     */
    protected $_coreString = null;

    /**
     * Class constructor
     *
     *
     *
     * @param Magento_Core_Helper_String $coreString
     * @param Magento_Core_Model_Resource $resource
     */
    public function __construct(
        Magento_Core_Helper_String $coreString,
        Magento_Core_Model_Resource $resource
    ) {
        $this->_coreString = $coreString;
        parent::__construct($resource);
    }

    /**
     * Define main table and primary index
     *
     */
    protected function _construct()
    {
        $this->_init('tag', 'tag_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Magento_Tag_Model_Resource_Tag
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => 'name',
            'title' => __('Tag')
        ));
        return $this;
    }

    /**
     * Loading tag by name
     *
     * @param Magento_Tag_Model_Tag $model
     * @param string $name
     * @return array|false
     */
    public function loadByName($model, $name)
    {
        if ( $name ) {
            $read = $this->_getReadAdapter();
            $select = $read->select();
            if ($this->_coreString->strlen($name) > 255) {
                $name = $this->_coreString->substr($name, 0, 255);
            }

            $select->from($this->getMainTable())
                ->where('name = :name');
            $data = $read->fetchRow($select, array('name' => $name));

            $model->setData(( is_array($data) ) ? $data : array());
        } else {
            return false;
        }
    }

    /**
     * Before saving actions
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Tag_Model_Resource_Tag
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        if (!$object->getId() && $object->getStatus() == $object->getApprovedStatus()) {
            $searchTag = new Magento_Object();
            $this->loadByName($searchTag, $object->getName());
            if ($searchTag->getData($this->getIdFieldName())
                    && $searchTag->getStatus() == $object->getPendingStatus()) {
                $object->setId($searchTag->getData($this->getIdFieldName()));
            }
        }

        if ($this->_coreString->strlen($object->getName()) > 255) {
            $object->setName($this->_coreString->substr($object->getName(), 0, 255));
        }

        return parent::_beforeSave($object);
    }

    /**
     * Saving tag's base popularity
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        if (!$object->getStore() || !Mage::app()->getStore()->isAdmin()) {
            return parent::_afterSave($object);
        }

        $tagId = ($object->isObjectNew()) ? $object->getTagId() : $object->getId();

        $writeAdapter = $this->_getWriteAdapter();
        $writeAdapter->insertOnDuplicate($this->getTable('tag_properties'), array(
            'tag_id'            => $tagId,
            'store_id'          => $object->getStore(),
            'base_popularity'   => (!$object->getBasePopularity()) ? 0 : $object->getBasePopularity()
        ));

        return parent::_afterSave($object);
    }

    /**
     * Decrementing tag products quantity as action for product delete
     *
     * @param array $tagsId
     * @return int The number of affected rows
     */
    public function decrementProducts(array $tagsId)
    {
        $writeAdapter = $this->_getWriteAdapter();
        if (empty($tagsId)) {
            return 0;
        }

        return $writeAdapter->update(
            $this->getTable('tag_summary'),
            array('products' => new Zend_Db_Expr('products - 1')),
            array('tag_id IN (?)' => $tagsId)
        );
    }

    /**
     * Retrieve select object for load object data
     * Redeclare parent method just for adding tag's base popularity if flag exists
     *
     * @param string $field
     * @param mixed $value
     * @param Magento_Core_Model_Abstract $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getAddBasePopularity() && $object->hasStoreId()) {
            $select->joinLeft(
                array('properties' => $this->getTable('tag_properties')),
                "properties.tag_id = {$this->getMainTable()}.tag_id AND properties.store_id = {$object->getStoreId()}",
                'base_popularity'
            );
        }
        return $select;
    }

    /**
     * Fetch store ids in which tag visible
     *
     * @param Magento_Tag_Model_Resource_Tag $object
     * @return Magento_Tag_Model_Resource_Tag
     */
    protected function _afterLoad(Magento_Core_Model_Abstract $object)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getTable('tag_summary'), array('store_id'))
            ->where('tag_id = :tag_id');
        $storeIds = $read->fetchCol($select, array('tag_id' => $object->getId()));

        $object->setVisibleInStoreIds($storeIds);

        return $this;
    }
}
