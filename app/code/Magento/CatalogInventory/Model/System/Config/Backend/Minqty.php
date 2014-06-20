<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\System\Config\Backend;

/**
 * Minimum product qty backend model
 */
class Minqty extends \Magento\Framework\App\Config\Value
{
    /**
     * Validate minimum product qty value
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $minQty = (int) $this->getValue() >= 0 ? (int) $this->getValue() : (int) $this->getOldValue();
        $this->setValue((string) $minQty);
        return $this;
    }
}
