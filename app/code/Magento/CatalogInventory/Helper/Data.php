<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Helper;

/**
 * Catalog Inventory default helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Error codes, that Catalog Inventory module can set to quote or quote items
     */
    const ERROR_QTY = 1;

    /**
     * Error qty increments
     */
    const ERROR_QTY_INCREMENTS = 2;
}
