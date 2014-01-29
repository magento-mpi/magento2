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
 * Eav Form Type Resource Collection
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Resource\Form\Type;

use Magento\Eav\Model\Entity\Type;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize collection model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Eav\Model\Form\Type', 'Magento\Eav\Model\Resource\Form\Type');
    }

    /**
     * Convert items array to array for select options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('type_id', 'label');
    }

    /**
     * Add Entity type filter to collection
     *
     * @param Type|int $entity
     * @return $this
     */
    public function addEntityTypeFilter($entity)
    {
        if ($entity instanceof Type) {
            $entity = $entity->getId();
        }

        $this->getSelect()
            ->join(
                array('form_type_entity' => $this->getTable('eav_form_type_entity')),
                'main_table.type_id = form_type_entity.type_id',
                array())
            ->where('form_type_entity.entity_type_id = ?', $entity);

        return $this;
    }
}
