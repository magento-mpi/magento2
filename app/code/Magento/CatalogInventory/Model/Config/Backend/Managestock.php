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
 * Catalog Inventory Manage Stock Config Backend Model
 *
 * @category   Magento
 * @package    Magento_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogInventory\Model\Config\Backend;

class Managestock
    extends \Magento\Core\Model\Config\Value
{
/**
     * After change Catalog Inventory Manage value process
     *
     * @return \Magento\CatalogInventory\Model\Config\Backend\Managestock
     */
    protected function _afterSave()
    {
        $newValue = $this->getValue();
        $oldValue = $this->_coreConfig->getValue(
            \Magento\CatalogSearch\Model\Fulltext::XML_PATH_CATALOG_SEARCH_TYPE,
            $this->getScope(),
            $this->getScopeId()
        );
        if ($newValue != $oldValue) {
            \Mage::getSingleton('Magento\CatalogInventory\Model\Stock\Status')->rebuild();
        }

        return $this;
    }
}
