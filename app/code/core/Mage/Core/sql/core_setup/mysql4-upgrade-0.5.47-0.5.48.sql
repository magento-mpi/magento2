update `core_config_data` set `value` = '0', `inherit` = '1' where `path` = 'carriers/dhl/active';
update `core_config_data` set `inherit` = '0' where `scope` = 'default' and `path` = 'carriers/dhl/active';

update `core_config_field` set `show_in_default` = '0', `show_in_website` = '0', `show_in_store` = '0' where `path` = 'carriers/dhl';

update `core_config_data` set `value` = '0', `inherit` = '1' where `path` = 'carriers/pickup/active';
update `core_config_data` set `inherit` = '0' where `scope` = 'default' and `path` = 'carriers/pickup/active';

update `core_config_field` set `show_in_default` = '0', `show_in_website` = '0', `show_in_store` = '0' where `path` = 'carriers/pickup';
