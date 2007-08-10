alter table `catalogrule` add column `sort_order` int UNSIGNED   NOT NULL;
alter table `catalogrule` drop key `to_date`, add index `sort_order` (`is_active`, `sort_order`, `to_date`, `from_date`);
    
alter table `catalogrule_product` add column `sort_order` int UNSIGNED   NOT NULL;
alter table `catalogrule_product` drop key `from_date`;
alter table `catalogrule_product` add unique `sort_order` (`from_date`, `to_date`, `store_id`, `customer_group_id`, `product_id`, `sort_order`);
alter table `catalogrule_product` change `from_date` `from_time` int UNSIGNED   NOT NULL ;
alter table `catalogrule_product` change `to_date` `to_time` int UNSIGNED   NOT NULL ;

alter table `salesrule` add column `is_advanced` tinyint UNSIGNED DEFAULT 1  NOT NULL;
alter table `salesrule` add column `sort_order` int UNSIGNED   NOT NULL;
alter table `salesrule` drop key `to_date`, add index `sort_order` (`is_active`, `sort_order`, `to_date`, `from_date`);

alter table `salesrule_product` add column `sort_order` int UNSIGNED   NOT NULL;
alter table `salesrule_product` drop key `from_date`, add unique `sort_order` (`from_date`, `to_date`, `store_id`, `customer_group_id`, `product_id`, `sort_order`);
alter table `salesrule_product` change `from_date` `from_time` int UNSIGNED   NOT NULL ;
alter table `salesrule_product` change `to_date` `to_time` int UNSIGNED   NOT NULL ;

replace into `customer_group` values (0,'Not logged in');