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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$this->startSetup()->run("

alter table `catalog_product_entity` add column `sku` varchar (64)  NOT NULL after `type_id` ,add index `sku` (`sku`);

select @entity_type_id:=entity_type_id from eav_entity_type where entity_type_code='catalog_product';
select @sku_attribute_id:=attribute_id from eav_attribute where attribute_code='sku' and entity_type_id=@entity_type_id;

update eav_attribute set backend_type='static' where attribute_id=@sku_attribute_id;

update catalog_product_entity, catalog_product_entity_varchar v set sku=v.value where catalog_product_entity.entity_type_id=@entity_type_id and attribute_id=@sku_attribute_id and v.entity_id=catalog_product_entity.entity_id and v.store_id=0;

delete from catalog_product_entity_varchar where attribute_id=@sku_attribute_id;

")->endSetup();