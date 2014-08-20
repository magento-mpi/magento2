<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Model\Indexer\TargetRule\Plugin;

class AttributeSet extends AbstractPlugin
{
    /**
     * Invalidate target rule indexer after deleting attribute set
     *
     * @param \Magento\Eav\Model\Entity\Attribute\Set $attributeSet
     *
     * @return \Magento\Eav\Model\Entity\Attribute\Set
     */
    public function afterDelete(\Magento\Eav\Model\Entity\Attribute\Set $attributeSet)
    {
        $this->invalidateIndexers();
        return $attributeSet;
    }
}
