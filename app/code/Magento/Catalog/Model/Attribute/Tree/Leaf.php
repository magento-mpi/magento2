<?php
/**
 * Class to prepare leaf data of attribute set tree
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Attribute\Tree;

class Leaf
{
    /**
     * @param \Magento\Eav\Model\Entity\Attribute $child
     * @param int $attributeSetId
     * @param bool $unassignable
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($child, $attributeSetId, $unassignable)
    {
        return array(
            'text'              => $child->getAttributeCode(),
            'id'                => $child->getAttributeId(),
            'cls'               => $unassignable ? 'leaf' : 'system-leaf',
            'allowDrop'         => false,
            'allowDrag'         => true,
            'leaf'              => true,
            'is_user_defined'   => $child->getIsUserDefined(),
            'is_configurable'   => 0,
            'is_unassignable'   => $unassignable,
            'entity_id'         => $child->getEntityAttributeId()
        );
    }

}
