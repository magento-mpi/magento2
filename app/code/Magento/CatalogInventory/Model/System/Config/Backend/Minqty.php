<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Minimum product qty backend model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogInventory\Model\System\Config\Backend;

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
        $minQty = (int)$this->getValue() >= 0 ? (int)$this->getValue() : (int)$this->getOldValue();
        $this->setValue((string)$minQty);
        return $this;
    }
}
