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
 * Eav Form Element Resource Collection
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Resource\Form\Element;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize collection model
     */
    protected function _construct()
    {
        $this->_init('\Magento\Eav\Model\Form\Element', '\Magento\Eav\Model\Resource\Form\Element');
    }

    /**
     * Add Form Type filter to collection
     *
     * @param \Magento\Eav\Model\Form\Type|int $type
     * @return \Magento\Eav\Model\Resource\Form\Element\Collection
     */
    public function addTypeFilter($type)
    {
        if ($type instanceof \Magento\Eav\Model\Form\Type) {
            $type = $type->getId();
        }

        return $this->addFieldToFilter('type_id', $type);
    }

    /**
     * Add Form Fieldset filter to collection
     *
     * @param \Magento\Eav\Model\Form\Fieldset|int $fieldset
     * @return \Magento\Eav\Model\Resource\Form\Element\Collection
     */
    public function addFieldsetFilter($fieldset)
    {
        if ($fieldset instanceof \Magento\Eav\Model\Form\Fieldset) {
            $fieldset = $fieldset->getId();
        }

        return $this->addFieldToFilter('fieldset_id', $fieldset);
    }

    /**
     * Add Attribute filter to collection
     *
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute|int $attribute
     *
     * @return \Magento\Eav\Model\Resource\Form\Element\Collection
     */
    public function addAttributeFilter($attribute)
    {
        if ($attribute instanceof \Magento\Eav\Model\Entity\Attribute\AbstractAttribute) {
            $attribute = $attribute->getId();
        }

        return $this->addFieldToFilter('attribute_id', $attribute);
    }

    /**
     * Set order by element sort order
     *
     * @return \Magento\Eav\Model\Resource\Form\Element\Collection
     */
    public function setSortOrder()
    {
        $this->setOrder('sort_order', self::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * Join attribute data
     *
     * @return \Magento\Eav\Model\Resource\Form\Element\Collection
     */
    protected function _joinAttributeData()
    {
        $this->getSelect()->join(
            array('eav_attribute' => $this->getTable('eav_attribute')),
            'main_table.attribute_id = eav_attribute.attribute_id',
            array('attribute_code', 'entity_type_id')
        );

        return $this;
    }

    /**
     * Load data (join attribute data)
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return \Magento\Eav\Model\Resource\Form\Element\Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $this->_joinAttributeData();
        }
        return parent::load($printQuery, $logQuery);
    }
}
