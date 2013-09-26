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
namespace Magento\Sales\Model\Resource\Quote\Address\Attribute\Backend;

class Region
    extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     */
    public function __construct(\Magento\Directory\Model\RegionFactory $regionFactory)
    {
        $this->_regionFactory = $regionFactory;
    }

    /**
     * Set region to the attribute
     *
     * @param \Magento\Object $object
     * @return \Magento\Sales\Model\Resource\Quote\Address\Attribute\Backend\Region
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
