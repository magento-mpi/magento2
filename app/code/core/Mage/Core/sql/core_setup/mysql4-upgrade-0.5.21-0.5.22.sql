update `core_config_field` set `source_model` = 'adminhtml/system_config_source_order_status' where `path` like 'payment/%/order_status';
update `core_config_field` set `source_model` = 'adminhtml/system_config_source_order_status' where `path` like 'paygate/%/order_status';
update `core_config_data` set `value` = '1' where `path` like 'paygate/%/order_status';
update `core_config_data` set `value` = '1' where `path` like 'payment/%/order_status';