<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * System config image field backend model for Zend PDF generator
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Config_Backend_Image_Adapter extends Magento_Core_Model_Config_Value
{
    /**
     * @var Magento_Core_Model_Image_AdapterFactory
     */
    protected $_imageFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Image_AdapterFactory $imageFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Image_AdapterFactory $imageFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
        $this->_imageFactory = $imageFactory;
    }

    /**
     * Checks if choosen image adapter available
     *
     * @throws Magento_Core_Exception if some of adapter dipendencies was not loaded
     * @return Magento_Backend_Model_Config_Backend_File
     */
    protected function _beforeSave()
    {
        try {
            $this->_imageFactory->create($this->getValue());
        } catch (Exception $e) {
            $message = __('The specified image adapter cannot be used because of: ' . $e->getMessage());
            throw new Magento_Core_Exception($message);
        }

        return $this;
    }
}
