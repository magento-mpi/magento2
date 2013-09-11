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
     * Process data after load
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $value = \Mage::helper('Magento\CatalogInventory\Helper\Minsaleqty')->makeArrayFieldValue($value);
        $this->setValue($value);
    }

    /**
     * Prepare data before save
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        $value = \Mage::helper('Magento\CatalogInventory\Helper\Minsaleqty')->makeStorableArrayFieldValue($value);
        $this->setValue($value);
    }
}
