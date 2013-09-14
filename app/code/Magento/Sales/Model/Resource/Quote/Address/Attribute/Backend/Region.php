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
 */
class Magento_Sales_Model_Resource_Quote_Address_Attribute_Backend_Region
    extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * @var Magento_Directory_Model_RegionFactory
     */
    protected $_regionFactory;

    /**
     * @param Magento_Directory_Model_RegionFactory $regionFactory
     */
    public function __construct(Magento_Directory_Model_RegionFactory $regionFactory)
    {
        $this->_regionFactory = $regionFactory;
    }

    /**
     * Set region to the attribute
     *
     * @param Magento_Object $object
     * @return Magento_Sales_Model_Resource_Quote_Address_Attribute_Backend_Region
     */
    public function beforeSave($object)
    {
        if (is_numeric($object->getRegion())) {
            $region = $this->_regionFactory->create()->load((int)$object->getRegion());
            if ($region) {
                $object->setRegionId($region->getId());
                $object->setRegion($region->getCode());
            }
        }

        return $this;
    }
}
