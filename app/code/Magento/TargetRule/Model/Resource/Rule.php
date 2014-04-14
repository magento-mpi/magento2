<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Model\Resource;

/**
 * TargetRule Rule Resource Model
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Rule extends \Magento\Rule\Model\Resource\AbstractResource
{
    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = array(
        'product' => array(
            'associations_table' => 'magento_targetrule_product',
            'rule_id_field' => 'rule_id',
            'entity_id_field' => 'product_id'
        )
    );

    /**
     * @var \Magento\Index\Model\Indexer
     */
    protected $_indexer;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Index\Model\Indexer $indexer
     */
    public function __construct(\Magento\Framework\App\Resource $resource, \Magento\Index\Model\Indexer $indexer)
    {
        $this->_indexer = $indexer;
        parent::__construct($resource);
    }

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_targetrule', 'rule_id');
    }

    /**
     * Get Customer Segment Ids by rule
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return array
     */
    public function getCustomerSegmentIds(\Magento\Framework\Model\AbstractModel $object)
    {
        $ids = $this->getReadConnection()->select()->from(
            $this->getTable('magento_targetrule_customersegment'),
            'segment_id'
        )->where(
            'rule_id = ?',
            $object->getId()
        )->query()->fetchAll(
            \Zend_Db::FETCH_COLUMN
        );
        return empty($ids) ? array() : $ids;
    }

    /**
     * Bind rule to customer segments
     *
     * @param int $ruleId
     * @param int[] $segmentIds
     * @return $this
     */
    public function saveCustomerSegments($ruleId, $segmentIds)
    {
        if (empty($segmentIds)) {
            $segmentIds = array();
        }
        $adapter = $this->_getWriteAdapter();
        foreach ($segmentIds as $segmentId) {
            if (!empty($segmentId)) {
                $adapter->insertOnDuplicate(
                    $this->getTable('magento_targetrule_customersegment'),
                    array('rule_id' => $ruleId, 'segment_id' => $segmentId),
                    array()
                );
            }
        }

        if (empty($segmentIds)) {
            $segmentIds = array(0);
        }

        $adapter->delete(
            $this->getTable('magento_targetrule_customersegment'),
            array('rule_id = ?' => $ruleId, 'segment_id NOT IN (?)' => $segmentIds)
        );
        return $this;
    }

    /**
     * Add customer segment ids to rule
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setData('customer_segment_ids', $this->getCustomerSegmentIds($object));
        return parent::_afterLoad($object);
    }

    /**
     * Save matched products for current rule and clean index
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\TargetRule\Model\Rule $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_afterSave($object);
        $segmentIds = $object->getUseCustomerSegment() ? $object->getCustomerSegmentIds() : array(0);
        $this->saveCustomerSegments($object->getId(), $segmentIds);

        $this->unbindRuleFromEntity($object->getId(), array(), 'product');
        $this->bindRuleToEntity($object->getId(), $object->getMatchingProductIds(), 'product');

        $typeId = !$object->isObjectNew() && $object->getOrigData(
            'apply_to'
        ) != $object->getData(
            'apply_to'
        ) ? null : $object->getData(
            'apply_to'
        );

        $this->_indexer->processEntityAction(
            new \Magento\Object(array('type_id' => $typeId)),
            \Magento\TargetRule\Model\Index::ENTITY_TARGETRULE,
            \Magento\TargetRule\Model\Index::EVENT_TYPE_CLEAN_TARGETRULES
        );

        return $this;
    }

    /**
     * Clean index
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\TargetRule\Model\Rule $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->_indexer->processEntityAction(
            new \Magento\Object(array('type_id' => $object->getData('apply_to'))),
            \Magento\TargetRule\Model\Index::ENTITY_TARGETRULE,
            \Magento\TargetRule\Model\Index::EVENT_TYPE_CLEAN_TARGETRULES
        );

        parent::_beforeDelete($object);
        return $this;
    }
}
