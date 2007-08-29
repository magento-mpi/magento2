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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

UPDATE `eav_attribute` SET `frontend_label` = 'Main Image' WHERE `attribute_code` = 'image' and `entity_type_id`=10;
UPDATE `eav_attribute` SET `frontend_label` = 'Thumbnail Image' WHERE `attribute_code` = 'small_image';
UPDATE `eav_attribute` SET `frontend_label` = 'Cost<br/>(For internal use)' WHERE `attribute_code` = 'cost';
UPDATE `eav_attribute` SET `frontend_label` = 'SEF URL Identifier<br/>(will replace product name)' WHERE `attribute_code` = 'url_key';
UPDATE `eav_attribute` SET `frontend_label` = 'Qty Uses Decimals' WHERE `attribute_code` = 'qty_is_decimal';

UPDATE `catalog_product_type` SET `code` = 'Configurable Product' WHERE `type_id` =3;
UPDATE `catalog_product_type` SET `code` = 'Grouped Product' WHERE `type_id` =4;
