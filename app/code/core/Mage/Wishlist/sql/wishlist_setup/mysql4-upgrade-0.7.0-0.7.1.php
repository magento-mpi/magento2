<?php

$this->startSetup()->run("

alter table `wishlist_item`
    ,add constraint `FK_WISHLIST_PRODUCT` foreign key (`product_id`) references `catalog_product_entity` (`entity_id`) on delete cascade  on update cascade
;

")->endSetup();
