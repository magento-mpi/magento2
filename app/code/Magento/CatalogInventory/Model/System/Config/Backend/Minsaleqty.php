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
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\CatalogInventory\Helper\Minsaleqty $catalogInventoryMinsaleqty
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\CatalogInventory\Helper\Minsaleqty $catalogInventoryMinsaleqty,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_catalogInventoryMinsaleqty = $catalogInventoryMinsaleqty;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Process data after load
     *
     * @return void
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $value = $this->_catalogInventoryMinsaleqty->makeArrayFieldValue($value);
        $this->setValue($value);
    }

    /**
     * Prepare data before save
     *
     * @return void
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        $value = $this->_catalogInventoryMinsaleqty->makeStorableArrayFieldValue($value);
        $this->setValue($value);
    }
}
