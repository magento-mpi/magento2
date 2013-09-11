<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GoogleShopping Attributes collection
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleShopping\Model\Resource\Attribute;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Whether to join attribute_set_id to attributes or not
     *
     * @var bool
     */
    protected $_joinAttributeSetFlag = true;

    protected function _construct()
    {
        $this->_init('Magento\GoogleShopping\Model\Attribute', 'Magento\GoogleShopping\Model\Resource\Attribute');
    }

    /**
     * Add attribute set filter
     *
     * @param int $attributeSetId
     * @param string $targetCountry two words ISO format
     * @return \Magento\GoogleShopping\Model\Resource\Attribute\Collection
     */
    public function addAttributeSetFilter($attributeSetId, $targetCountry)
    {
        if (!$this->getJoinAttributeSetFlag()) {
            return $this;
        }
        $this->getSelect()->where('attribute_set_id = ?', $attributeSetId);
        $this->getSelect()->where('target_country = ?', $targetCountry);
        return $this;
    }

    /**
     * Add type filter
     *
     * @param int $type_id
     * @return \Magento\GoogleShopping\Model\Resource\Attribute\Collection
     */
    public function addTypeFilter($type_id)
    {
        $this->getSelect()->where('main_table.type_id = ?', $type_id);
        return $this;
    }

    /**
     * Load collection data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return  \Magento\GoogleShopping\Model\Resource\Attribute\Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        if ($this->getJoinAttributeSetFlag()) {
            $this->_joinAttributeSet();
        }
        parent::load($printQuery, $logQuery);
        return $this;
    }

    /**
     * Join attribute sets data to select
     *
     * @return  \Magento\GoogleShopping\Model\Resource\Attribute\Collection
     */
    protected function _joinAttributeSet()
    {
        $this->getSelect()
            ->joinInner(
                array('types'=>$this->getTable('googleshopping_types')),
                'main_table.type_id=types.type_id',
                array('attribute_set_id' => 'types.attribute_set_id', 'target_country' => 'types.target_country'));
        return $this;
    }

    /**
     * Get flag - whether to join attribute_set_id to attributes or not
     *
     * @return bool
     */
    public function getJoinAttributeSetFlag()
    {
        return $this->_joinAttributeSetFlag;
    }

    /**
     * Set flag - whether to join attribute_set_id to attributes or not
     *
     * @param bool $flag
     * @return bool
     */
    public function setJoinAttributeSetFlag($flag)
    {
        return $this->_joinAttributeSetFlag = (bool)$flag;
    }
}
