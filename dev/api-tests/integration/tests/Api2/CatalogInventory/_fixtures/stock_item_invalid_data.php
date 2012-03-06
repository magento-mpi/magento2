<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

return array(
    'stock_id'                    => Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID,
    'manage_stock'                => -1,
    'use_config_manage_stock'     => -1,
    'qty'                         => 'text',
    'min_qty'                     => 'text',
    'use_config_min_qty'          => -1,
    'is_qty_decimal'              => -1,
    'backorders'                  => -1,
    'use_config_backorders'       => -1,
    'min_sale_qty'                => 'text',
    'use_config_min_sale_qty'     => -1,
    'max_sale_qty'                => 'text',
    'use_config_max_sale_qty'     => -1,
    'is_in_stock'                 => -1,
    'notify_stock_qty'            => 'text',
    'use_config_notify_stock_qty' => -1,
    'stock_status_changed_auto'   => -1,
    'use_config_qty_increments'   => -1,
    'qty_increments'              => 'text',
    'use_config_enable_qty_inc'   => -1,
    'enable_qty_increments'       => -1,
    'is_decimal_divided'          => -1,
);
