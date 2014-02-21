<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Entity\Collection;

class AbstractCollectionStub extends \Magento\Eav\Model\Entity\Collection\AbstractCollection
{
    /**
     * Retrieve item by id
     *
     * @param   mixed $id
     * @return  \Magento\Object
     */
    public function getItemById($id)
    {
        if (isset($this->_itemsById[$id])) {
            return $this->_itemsById[$id];
        }
        return null;
    }

    /**
     * Initialize collection
     *
     * @return void
     */
    protected function _construct()
    {
        return $this->_init('Magento\Object', 'test_entity_model');
    }
}
