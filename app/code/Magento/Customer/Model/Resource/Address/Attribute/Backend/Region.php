<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Address region attribute backend
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Resource_Address_Attribute_Backend_Region
    extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Prepare object for save
     *
     * @param \Magento\Object $object
     * @return Magento_Customer_Model_Resource_Address_Attribute_Backend_Region
     */
    public function beforeSave($object)
    {
        $region = $object->getData('region');
        if (is_numeric($region)) {
            $regionModel = Mage::getModel('Magento_Directory_Model_Region')->load($region);
            if ($regionModel->getId() && $object->getCountryId() == $regionModel->getCountryId()) {
                $object->setRegionId($regionModel->getId())
                    ->setRegion($regionModel->getName());
            }
        }
        return $this;
    }
}
