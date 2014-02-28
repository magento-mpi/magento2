<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Quote\Address\Attribute\Backend;

/**
 * Quote address attribute backend region resource model
 */
class Region
    extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * @param \Magento\Logger $logger
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\Directory\Model\RegionFactory $regionFactory
    ) {
        $this->_regionFactory = $regionFactory;
        parent::__construct($logger);
    }

    /**
     * Set region to the attribute
     *
     * @param \Magento\Object $object
     * @return $this
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
