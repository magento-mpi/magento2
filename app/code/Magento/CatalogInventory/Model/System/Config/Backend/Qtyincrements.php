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
 * Backend for qty increments
 *
 */
namespace Magento\CatalogInventory\Model\System\Config\Backend;

use Magento\Core\Exception;

class Qtyincrements extends \Magento\Core\Model\Config\Value
{
    /**
     * Validate data before save
     *
     * @return void
     * @throws Exception
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (floor($value) != $value) {
            throw new Exception('Decimal qty increments is not allowed.');
        }
    }
}
