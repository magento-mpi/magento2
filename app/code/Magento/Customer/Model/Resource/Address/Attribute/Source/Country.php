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
 * Customer country attribute source
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Resource_Address_Attribute_Source_Country extends Magento_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * @var Magento_Directory_Model_Resource_Country_CollectionFactory
     */
    protected $_countriesFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Option_CollectionFactory $attrOptCollFactory
     * @param Magento_Eav_Model_Resource_Entity_Attribute_OptionFactory $attrOptionFactory
     * @param Magento_Directory_Model_Resource_Country_CollectionFactory $countriesFactory
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Eav_Model_Resource_Entity_Attribute_Option_CollectionFactory $attrOptCollFactory,
        Magento_Eav_Model_Resource_Entity_Attribute_OptionFactory $attrOptionFactory,
        Magento_Directory_Model_Resource_Country_CollectionFactory $countriesFactory
    ) {
        $this->_countriesFactory = $countriesFactory;
        parent::__construct($coreData, $attrOptCollFactory, $attrOptionFactory);
    }

    /**
     * Retreive all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_createCountriesCollection()
                ->loadByStore($this->getAttribute()->getStoreId())->toOptionArray();
        }
        return $this->_options;
    }

    /**
     * @return Magento_Directory_Model_Resource_Country_Collection
     */
    protected function _createCountriesCollection()
    {
        return $this->_countriesFactory->create();
    }
}
