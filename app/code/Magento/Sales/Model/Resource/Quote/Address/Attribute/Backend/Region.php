<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Quote address attribute backend region resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Quote\Address\Attribute\Backend;

class Region
    extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Set region to the attribute
     *
     * @param \Magento\Object $object
     * @return \Magento\Sales\Model\Resource\Quote\Address\Attribute\Backend\Region
     */
    public function beforeSave($object)
    {
        if (is_numeric($object->getRegion())) {
            $region = \Mage::getModel('Magento\Directory\Model\Region')->load((int)$object->getRegion());
            if ($region) {
                $object->setRegionId($region->getId());
                $object->setRegion($region->getCode());
            }
        }

        return $this;
    }
}
