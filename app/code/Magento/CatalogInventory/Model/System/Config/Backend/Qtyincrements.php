<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\System\Config\Backend;

use Magento\Framework\Model\Exception;

/**
 * Backend for qty increments
 */
class Qtyincrements extends \Magento\Framework\App\Config\Value
{
    /**
     * Validate data before save
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (floor($value) != $value) {
            throw new Exception('Decimal qty increments is not allowed.');
        }
    }
}
