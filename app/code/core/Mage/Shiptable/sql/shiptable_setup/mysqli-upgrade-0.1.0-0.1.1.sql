alter table `shiptable_data` 
    , change `dest_country` `dest_country_id` int (10)  NOT NULL  COLLATE latin1_swedish_ci 
    , change `dest_region` `dest_region_id` int (10)  NOT NULL  COLLATE latin1_swedish_ci ;

insert into `shiptable_data` (dest_country_id, dest_region_id, condition_name, condition_value, price, cost) values 
    (223, 1, 'package_weight', 100, 10, 5),    
    (223, 1, 'package_weight', 1000, 20, 10);