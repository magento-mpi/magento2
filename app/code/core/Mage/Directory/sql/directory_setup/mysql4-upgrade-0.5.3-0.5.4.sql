
alter table `directory_currency_name` ,change `format` `output_format` varchar (32)  DEFAULT '%s' NOT NULL  COLLATE utf8_general_ci 