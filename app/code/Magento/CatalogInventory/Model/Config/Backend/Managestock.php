<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Inventory Manage Stock Config Backend Model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogInventory\Model\Config\Backend;

class Managestock extends AbstractValue
{
    /**
     * After change Catalog Inventory Manage value process
     *
     * @return $this
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $this->_stockStatus->rebuild();
        }

        return $this;
    }
}
