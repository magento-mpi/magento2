<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Catalog Product Attributes Backend Model
 *
 * @category   Magento
 * @package    Magento_TargetRule
 */
namespace Magento\TargetRule\Model\Catalog\Product\Attribute\Backend;

class Rule extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Before attribute save prepare data
     *
     * @param \Magento\Catalog\Model\Product $object
     * @return \Magento\TargetRule\Model\Catalog\Product\Attribute\Backend\Rule
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();
        $useDefault = $object->getData($attributeName . '_default');

        if ($useDefault == 1) {
            $object->setData($attributeName, null);
        }

        return $this;
    }
}
