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
namespace Magento\Customer\Model\Resource\Address\Attribute\Backend;

class Region
    extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        array $data = array()
    ) {
        $this->_regionFactory = $regionFactory;
        parent::__construct($logger, $data);
    }

    /**
     * Prepare object for save
     *
     * @param \Magento\Object $object
     * @return \Magento\Customer\Model\Resource\Address\Attribute\Backend\Region
     */
    public function beforeSave($object)
    {
        $region = $object->getData('region');
        if (is_numeric($region)) {
            $regionModel = $this->_createRegionInstance();
            $regionModel->load($region);
            if ($regionModel->getId() && $object->getCountryId() == $regionModel->getCountryId()) {
                $object->setRegionId($regionModel->getId())
                    ->setRegion($regionModel->getName());
            }
        }
        return $this;
    }

    /**
     * @return \Magento\Directory\Model\Region
     */
    protected function _createRegionInstance()
    {
        return $this->_regionFactory->create();
    }
}
