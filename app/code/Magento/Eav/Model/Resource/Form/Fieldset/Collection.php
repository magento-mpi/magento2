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
 * Eav Form Fieldset Resource Collection
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Resource\Form\Fieldset;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Store scope ID
     *
     * @var int
     */
    protected $_storeId;

    /**
     * Initialize collection model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Eav\Model\Form\Fieldset', 'Magento\Eav\Model\Resource\Form\Fieldset');
    }

    /**
     * Add Form Type filter to collection
     *
     * @param \Magento\Eav\Model\Form\Type|int $type
     * @return \Magento\Eav\Model\Resource\Form\Fieldset\Collection
     */
    public function addTypeFilter($type)
    {
        if ($type instanceof \Magento\Eav\Model\Form\Type) {
            $type = $type->getId();
        }

        return $this->addFieldToFilter('type_id', $type);
    }

    /**
     * Set order by fieldset sort order
     *
     * @return \Magento\Eav\Model\Resource\Form\Fieldset\Collection
     */
    public function setSortOrder()
    {
        $this->setOrder('sort_order', self::SORT_ORDER_ASC);
        return $this;
    }

    /**
     * Retrieve label store scope
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            return \Mage::app()->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * Set store scope ID
     *
     * @param int $storeId
     * @return \Magento\Eav\Model\Resource\Form\Fieldset\Collection
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Initialize select object
     *
     * @return \Magento\Eav\Model\Resource\Form\Fieldset\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $select = $this->getSelect();
        $select->join(
            array('default_label' => $this->getTable('eav_form_fieldset_label')),
            'main_table.fieldset_id = default_label.fieldset_id AND default_label.store_id = 0',
            array());
        if ($this->getStoreId() == 0) {
            $select->columns('label', 'default_label');
        } else {
            $labelExpr = $select->getAdapter()
                ->getIfNullSql('store_label.label', 'default_label.label');
            $joinCondition = $this->getConnection()
                ->quoteInto(
                    'main_table.fieldset_id = store_label.fieldset_id AND store_label.store_id = ?', 
                    (int)$this->getStoreId());
            $select->joinLeft(
                array('store_label' => $this->getTable('eav_form_fieldset_label')),
                $joinCondition,
                array('label' => $labelExpr)
            );
        }

        return $this;
    }
}
