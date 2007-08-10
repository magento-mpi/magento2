alter table `core_store` add column `sort_order` smallint (5)UNSIGNED   NOT NULL;
alter table `core_store` add column `is_active` tinyint (1)UNSIGNED   NOT NULL ;

alter table `core_website` add column `sort_order` smallint (5)UNSIGNED   NOT NULL ;
alter table `core_website` add column `is_active` tinyint (1)UNSIGNED   NOT NULL;

update `core_store` set `is_active`=1;
update `core_website` set `is_active`=1;

alter table `core_website` add unique `code` (`code`);
alter table `core_website` add index `is_active` (`is_active`, `sort_order`);

alter table `core_store` add index `is_active` (`is_active`, `sort_order`);