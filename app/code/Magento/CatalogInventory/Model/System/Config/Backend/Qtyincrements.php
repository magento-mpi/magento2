<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend for qty increments
 *
 */
namespace Magento\CatalogInventory\Model\System\Config\Backend;

use Magento\Framework\Model\Exception;

class Qtyincrements extends \Magento\Framework\App\Config\Value
{
    /**
     * Validate data before save
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (floor($value) != $value) {
            throw new Exception('Decimal qty increments is not allowed.');
        }
    }
}
