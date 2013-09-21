<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration source for customer group multiselect
 *
 * @category   Magento
 * @package    Magento_CatalogPermissions
 */
class Magento_CatalogPermissions_Model_Adminhtml_System_Config_Source_Customer_Group
    implements Magento_Core_Model_Option_ArrayInterface
{
    protected $_options;

    /**
     * @var Magento_Customer_Model_Resource_Group_CollectionFactory
     */
    protected $_groupCollFactory;

    /**
     * @param Magento_Customer_Model_Resource_Group_CollectionFactory $groupCollFactory
     */
    public function __construct(Magento_Customer_Model_Resource_Group_CollectionFactory $groupCollFactory)
    {
        $this->_groupCollFactory = $groupCollFactory;
    }

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->_groupCollFactory->create()
                ->loadData()
                ->toOptionArray();
        }
        return $this->_options;
    }
}
