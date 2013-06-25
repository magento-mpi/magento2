<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * System config image field backend model for Zend PDF generator
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Image_Adapter extends Mage_Core_Model_Config_Data
{
    /**
     * @var Mage_Core_Model_Image_AdapterFactory
     */
    protected $_imageFactory;

    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Core_Model_Context $context
     * @param Mage_Core_Model_Image_AdapterFactory $imageFactory
     * @param Mage_Core_Helper_Data $helper
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Context $context,
        Mage_Core_Model_Image_AdapterFactory $imageFactory,
        Mage_Core_Helper_Data $helper,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
        $this->_imageFactory = $imageFactory;
        $this->_helper = $helper;
    }

    /**
     * Checks if choosen image adapter available
     *
     * @throws Mage_Core_Exception if some of adapter dipendencies was not loaded
     * @return Mage_Backend_Model_Config_Backend_File
     */
    protected function _beforeSave()
    {
        try {
            $this->_imageFactory->create($this->getValue());
        } catch (Exception $e) {
            $message = $this->_helper->__('The specified image adapter cannot be used because of: ' . $e->getMessage());
            throw new Mage_Core_Exception($message);
        }

        return $this;
    }
}
