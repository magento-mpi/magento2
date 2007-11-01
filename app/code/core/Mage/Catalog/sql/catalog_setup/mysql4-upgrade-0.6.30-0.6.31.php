<?php

$this->startSetup()->run("

alter table `catalog_product_entity` add column `sku` varchar (64)  NOT NULL after `type_id` ,add index `sku` (`sku`);

select @entity_type_id:=entity_type_id from eav_entity_type where entity_type_code='catalog_product';
select @sku_attribute_id:=attribute_id from eav_attribute where attribute_code='sku' and entity_type_id=@entity_type_id;

update eav_attribute set backend_type='static' where attribute_id=@sku_attribute_id;

update catalog_product_entity, catalog_product_entity_varchar v set sku=v.value where catalog_product_entity.entity_type_id=@entity_type_id and attribute_id=@sku_attribute_id and v.entity_id=catalog_product_entity.entity_id and v.store_id=0;

delete from catalog_product_entity_varchar where attribute_id=@sku_attribute_id;

")->endSetup();