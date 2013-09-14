<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer store_id attribute source
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Entity_Attribute_Source_Store extends Magento_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * @var Magento_Core_Model_Resource_Store_CollectionFactory
     */
    protected $_storeCollFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Option_CollectionFactory $attrOptCollFactory
     * @param Magento_Eav_Model_Resource_Entity_Attribute_OptionFactory $attrOptionFactory
     * @param Magento_Core_Model_Resource_Store_CollectionFactory $storeCollFactory
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Eav_Model_Resource_Entity_Attribute_Option_CollectionFactory $attrOptCollFactory,
        Magento_Eav_Model_Resource_Entity_Attribute_OptionFactory $attrOptionFactory,
        Magento_Core_Model_Resource_Store_CollectionFactory $storeCollFactory
    ) {
        parent::__construct($coreData, $attrOptCollFactory, $attrOptionFactory);
        $this->_storeCollFactory = $storeCollFactory;
    }

    /**
     * Retrieve Full Option values array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = $this->_storeCollFactory->create()
                ->load()
                ->toOptionArray();
        }
        return $this->_options;
    }
}
