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
 * Customer region attribute source
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Resource_Address_Attribute_Source_Region extends Magento_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * @var Magento_Directory_Model_Resource_Region_CollectionFactory
     */
    protected $_regionsFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Option_CollectionFactory $attrOptCollFactory
     * @param Magento_Eav_Model_Resource_Entity_Attribute_OptionFactory $attrOptionFactory
     * @param Magento_Directory_Model_Resource_Region_CollectionFactory $regionsFactory
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Eav_Model_Resource_Entity_Attribute_Option_CollectionFactory $attrOptCollFactory,
        Magento_Eav_Model_Resource_Entity_Attribute_OptionFactory $attrOptionFactory,
        Magento_Directory_Model_Resource_Region_CollectionFactory $regionsFactory
    ) {
        $this->_regionsFactory = $regionsFactory;
        parent::__construct($coreData, $attrOptCollFactory, $attrOptionFactory);
    }

    /**
     * Retreive all region options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_createRegionsCollection()->load()->toOptionArray();
        }
        return $this->_options;
    }

    /**
     * @return Magento_Directory_Model_Resource_Region_Collection
     */
    protected function _createRegionsCollection()
    {
        return $this->_regionsFactory->create();
    }
}
