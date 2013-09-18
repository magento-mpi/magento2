<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend for serialized array data
 *
 */
namespace Magento\CatalogInventory\Model\System\Config\Backend;

class Minsaleqty extends \Magento\Core\Model\Config\Value
{
    /**
     * Catalog inventory minsaleqty
     *
     * @var \Magento\CatalogInventory\Helper\Minsaleqty
     */
    protected $_catalogInventoryMinsaleqty = null;

    /**
     * @param Magento_CatalogInventory_Helper_Minsaleqty $catalogInventoryMinsaleqty
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\CatalogInventory\Helper\Minsaleqty $catalogInventoryMinsaleqty,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_catalogInventoryMinsaleqty = $catalogInventoryMinsaleqty;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Process data after load
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $value = $this->_catalogInventoryMinsaleqty->makeArrayFieldValue($value);
        $this->setValue($value);
    }

    /**
     * Prepare data before save
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        $value = $this->_catalogInventoryMinsaleqty->makeStorableArrayFieldValue($value);
        $this->setValue($value);
    }
}
