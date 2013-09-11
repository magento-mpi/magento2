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
 * Eav attribute group resource collection
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Resource\Entity\Attribute\Group;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Init resource model for collection
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Eav\Model\Entity\Attribute\Group', 'Magento\Eav\Model\Resource\Entity\Attribute\Group');
    }

    /**
     * Set Attribute Set Filter
     *
     * @param int $setId
     * @return \Magento\Eav\Model\Resource\Entity\Attribute\Group\Collection
     */
    public function setAttributeSetFilter($setId)
    {
        $this->addFieldToFilter('attribute_set_id', array('eq' => $setId));
        $this->setOrder('sort_order');
        return $this;
    }

    /**
     * Set sort order
     *
     * @param string $direction
     * @return \Magento\Eav\Model\Resource\Entity\Attribute\Group\Collection
     */
    public function setSortOrder($direction = self::SORT_ORDER_ASC)
    {
        return $this->addOrder('sort_order', $direction);
    }
}
