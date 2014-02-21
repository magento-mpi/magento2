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
    public function getItemById($id)
    {
        if (isset($this->_itemsById[$id])) {
            return $this->_itemsById[$id];
        }
        return null;
    }

    protected function _construct()
    {
        return $this->_init('Magento\Object', 'test_entity_model');
    }
}
