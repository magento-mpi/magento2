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
     * @var Magento_Directory_Model_RegionFactory
     */
    protected $_regionFactory;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Directory_Model_RegionFactory $regionFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Directory_Model_RegionFactory $regionFactory,
        array $data = array()
    ) {
        $this->_regionFactory = $regionFactory;
        parent::__construct($logger, $data);
    }

    /**
     * Prepare object for save
     *
     * @param Magento_Object $object
     * @return Magento_Customer_Model_Resource_Address_Attribute_Backend_Region
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
     * @return Magento_Directory_Model_Region
     */
    protected function _createRegionInstance()
    {
        return $this->_regionFactory->create();
    }
}
