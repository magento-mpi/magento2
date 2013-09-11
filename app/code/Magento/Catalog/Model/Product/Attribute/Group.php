<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Attribute;

class Group extends \Magento\Eav\Model\Entity\Attribute\Group
{

    /**
     * Check if group contains system attributes
     *
     * @return bool
     */
    public function hasSystemAttributes()
    {
        $result = false;
        /** @var $attributesCollection \Magento\Catalog\Model\Resource\Product\Attribute\Collection */
        $attributesCollection = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Attribute\Collection');
        $attributesCollection->setAttributeGroupFilter($this->getId());
        foreach ($attributesCollection as $attribute) {
            if (!$attribute->getIsUserDefined()) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    /**
     * Check if contains attributes used in the configurable products
     *
     * @return bool
     */
    public function hasConfigurableAttributes()
    {
        $result = false;
        /** @var $attributesCollection \Magento\Catalog\Model\Resource\Product\Attribute\Collection */
        $attributesCollection = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Attribute\Collection');
        $attributesCollection->setAttributeGroupFilter($this->getId());
        foreach ($attributesCollection as $attribute) {
            if ($attribute->getIsConfigurable()) {
                $result = true;
                break;
            }
        }
        return $result;
    }
}
