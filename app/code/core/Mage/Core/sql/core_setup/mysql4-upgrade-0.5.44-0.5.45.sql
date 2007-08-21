/*
alter table `shipping_tablerate` drop key `dest_country`, add unique key `dest_country` (`website_id`, `dest_country_id`, `dest_region_id`, `dest_zip`, `condition_name`, `condition_value`);

*/
