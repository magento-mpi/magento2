<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Source_Store implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @var Magento_Core_Model_Resource_Store_CollectionFactory
     */
    protected $_storesFactory;

    /**
     * @param Magento_Core_Model_Resource_Store_CollectionFactory $storesFactory
     */
    public function __construct(Magento_Core_Model_Resource_Store_CollectionFactory $storesFactory)
    {
        $this->_storesFactory = $storesFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            /** @var $stores Magento_Core_Model_Resource_Store_Collection */
            $stores = $this->_storesFactory->create();
            $this->_options = $stores->load()->toOptionArray();
        }
        return $this->_options;
    }
}
